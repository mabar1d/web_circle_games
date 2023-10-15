<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ContentTagsModel;
use App\Models\JobNotifFirebaseModel;
use App\Models\NewsModel;
use App\Models\TagsModel;
use App\Models\UserApkModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NewsController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('backend.news.index', []);
    }

    public function getDatatable(Request $request)
    {
        if (request()->ajax()) {
            $requestData = $request->input();
            $listNews = NewsModel::select(
                'id',
                'news_category_id',
                'title',
                'slug',
                'content',
                'image',
                'status',
                'created_by'
            );
            // dd(NewsModel::with('newsTags')->get()->toArray());
            return DataTables::of($listNews)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $result = isset($row['status']) && $row['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    return $result;
                })
                ->addColumn('news_category_name', function ($row) {
                    $result = isset($row->newsCategory) ? $row->newsCategory->name : null;
                    return $result;
                })
                ->addColumn('tag', function ($row) {
                    $result = null !== $row->pivotNewsTags->pluck("name") && $row->pivotNewsTags->pluck("name") ? implode(", ", $row->pivotNewsTags->pluck("name")->toArray()) : NULL;
                    return $result;
                })
                ->addColumn('action', function ($row) {
                    $result = "";
                    $result .= '<button type="button" class="btn btn-warning btn-xs m-1 btnView" data-id="' . $row['id'] . '">View</button>';
                    $result .= '<button type="button" class="btn btn-danger btn-xs m-1 btnDelete" data-id="' . $row['id'] . '">Delete</button>';
                    return $result;
                })
                ->rawColumns(['status', 'action'])
                ->make();
        }
    }

    public function getFormAdd(Request $request)
    {
        try {
            if (request()->ajax()) {
                $requestData = $request->input();
                $newsId = isset($requestData["id"]) && $requestData["id"] ? $requestData["id"] : null;
                $isDisabled = false;
                $data = NULL;
                if ($newsId) {
                    $isDisabled = true;
                    $data = NewsModel::findOrFail($newsId);
                    $newsCategoryName = isset($data->newsCategory) ? $data->newsCategory->name : NULL;
                    $newsTags = $data->pivotNewsTags->pluck("name", "id")->toArray();
                    $data = $data->toArray();
                    $data['news_category_name'] = $newsCategoryName;
                    $data['array_tags'] = $newsTags;
                    $data['news_image_url'] = env('URL_API_CIRCLE_GAMES') . "/upload/news/" . $data["image"];
                }
                $throwData = [
                    "data" => $data,
                    "isDisabled" => $isDisabled
                ];
                return view('backend/news/modalFormAdd', $throwData);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function store(Request $request)
    {
        $response = array(
            "code" => 1,
            "message" => ""
        );
        DB::beginTransaction();
        try {
            $requestData = $request->input();
            // dd($requestData);
            if (!isset($requestData["newsTitle"]) || !$requestData["newsTitle"]) {
                throw new Exception("News Title is Empty!", 1);
            }
            if (!isset($requestData["newsCategory"]) || !$requestData["newsCategory"]) {
                throw new Exception("Category News is Empty!", 1);
            }
            $newsId = isset($requestData["newsId"]) && $requestData["newsId"] ? $requestData["newsId"] : NULL;
            $newsCategory = isset($requestData["newsCategory"]) && $requestData["newsCategory"] ? $requestData["newsCategory"] : NULL;
            $title = isset($requestData["newsTitle"]) && $requestData["newsTitle"] ? trim($requestData["newsTitle"]) : NULL;
            $content = isset($requestData["newsContent"]) && $requestData["newsContent"] ? trim($requestData["newsContent"]) : NULL;
            // $image = isset($requestData["newsImage"]) && $requestData["newsImage"] ? trim($requestData["newsImage"]) : NULL;
            $status = isset($requestData["newsStatus"]) && $requestData["newsStatus"] ? $requestData["newsStatus"] : 0;
            $tags = isset($requestData["newsTags"]) && $requestData["newsTags"] ? $requestData["newsTags"] : 0;

            $dataStore = array(
                "news_category_id" => $newsCategory,
                "title" => $title,
                "slug" => Str::slug($title),
                "content" => $content,
                "status" => $status
            );

            //upload image to api
            if (request('newsImage')) {
                $file               = request('newsImage');
                $file_path          = $file->getPathname();
                $file_mime          = $file->getMimeType('image');
                $file_uploaded_name = $file->getClientOriginalName();
                $api_url = env('URL_API_CIRCLE_GAMES') . '/api/uploadImageNews';

                $client = new \GuzzleHttp\Client();

                $responseApi = $client->request("POST", $api_url, [
                    // jika menggunakan authorization
                    'headers' => ['Authorization' => 'Bearer ' . env('GOD_BEARER_TOKEN')],
                    'multipart' => [
                        [
                            'name' => 'image_file',
                            'filename' => $file_uploaded_name,
                            'Mime-Type' => $file_mime,
                            'contents' => fopen($file_path, 'r'),
                        ]
                    ],
                    'verify' => false
                ]);

                $responseApi   = $responseApi->getBody();
                $responseApiData = json_decode($responseApi, true);
                if ($responseApiData["code"] != '00') {
                    throw new Exception($responseApiData["desc"], 1);
                }
                $dataStore["image"] = isset($responseApiData["data"]["filename"]) && $responseApiData["data"]["filename"] ? $responseApiData["data"]["filename"] : NULL;
            }

            if (!$newsId) {
                $checkDataExist = NewsModel::where("title", $title)->count();
                if ($checkDataExist != 0) {
                    throw new Exception("News Already Exist!", 1);
                }
            }
            $storeNews = NewsModel::updateOrCreate(
                [
                    "id" => $newsId
                ],
                $dataStore
            );
            if (!$storeNews) {
                throw new Exception("Error Store Data!", 1);
            }

            //delete all news tag where not in update tag in table news_with_tag
            ContentTagsModel::where("content_type", 'news')->where("content_id", $storeNews["id"])->delete();
            if (is_array($tags)) {
                $arrayNewsTagId = array();
                foreach ($tags as $rowNewsTag) {
                    $getNewsTag = TagsModel::where("name", strtolower($rowNewsTag))->first();
                    if (!$getNewsTag) {
                        $createdNewsTag = TagsModel::create([
                            "name" => strtolower($rowNewsTag)
                        ]);
                    }
                    $newsTagId = isset($getNewsTag["id"]) ? (int)$getNewsTag["id"] : (int)$createdNewsTag->id;
                    $arrayNewsTagId[] = $newsTagId;
                    $insertNewsWithTag = array(
                        "content_type" => "news",
                        "content_id" => $storeNews["id"],
                        "tag_id" => $newsTagId
                    );
                    ContentTagsModel::updateOrcreate($insertNewsWithTag);
                }
            }

            if ($storeNews->wasRecentlyCreated) {
                $getAllUserApk = UserApkModel::select("token_firebase")
                    ->whereNotNull("token_firebase")
                    ->get();
                foreach ($getAllUserApk as $rowAllUserApk) {
                    // updateOrCreate performed create
                    JobNotifFirebaseModel::create(array(
                        "notif_type" => "news",
                        "client_key" => $rowAllUserApk["token_firebase"],
                        "notif_title" => $title,
                        "notif_body" => substr($content, 0, 100),
                        "notif_img_url" => env('URL_API_CIRCLE_GAMES') . "/upload/news/" . $dataStore["image"],
                        "notif_url" => "",
                        "status" => 0
                    ));
                }
            }

            $response = array(
                "code" => 0,
                "message" => "Success Store Data"
            );
            DB::commit();
        } catch (Exception $e) {
            $response = array(
                "code" => $e->getCode(),
                "message" => $e->getMessage()
            );
            DB::rollBack();
        }
        return json_encode($response);
    }

    public function delete(Request $request)
    {
        $response = array(
            "code" => 1,
            "message" => ""
        );
        DB::beginTransaction();
        try {
            $requestData = $request->input();
            $newsId = isset($requestData["id"]) && $requestData["id"] ? $requestData["id"] : NULL;
            if (!$newsId) {
                throw new Exception("News Id is Empty!", 1);
            }
            NewsModel::findOrFail($newsId)->delete();
            $response = array(
                "code" => 0,
                "message" => "Success Delete Data"
            );
            DB::commit();
        } catch (Exception $e) {
            $response = array(
                "code" => $e->getCode(),
                "message" => $e->getMessage()
            );
            DB::rollBack();
        }
        return json_encode($response);
    }
}
