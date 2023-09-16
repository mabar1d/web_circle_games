<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ApiCircleGamesHelper;
use App\Http\Controllers\Controller;
use App\Models\NewsModel;
use App\Models\NewsTagsModel;
use App\Models\TagsModel;
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
                    $result = isset($row->newsCategory->name) && $row->newsCategory->name ? $row->newsCategory->name : null;
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
                    $newsCategoryName = $data->newsCategory->name;
                    $newsTags = $data->pivotNewsTags->pluck("name", "id")->toArray();
                    $data = $data->toArray();
                    $data['news_category_name'] = $newsCategoryName;
                    $data['array_tags'] = $newsTags;
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
            $image = isset($requestData["newsImage"]) && $requestData["newsImage"] ? trim($requestData["newsImage"]) : NULL;
            $status = isset($requestData["newsStatus"]) && $requestData["newsStatus"] ? $requestData["newsStatus"] : 0;
            $tags = isset($requestData["newsTags"]) && $requestData["newsTags"] ? $requestData["newsTags"] : 0;
            $dataStore = array(
                "news_category_id" => $newsCategory,
                "title" => $title,
                "slug" => Str::slug($title),
                "content" => $content,
                "image" => $image,
                "status" => $status
            );
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
            NewsTagsModel::where("news_id", $storeNews["id"])->delete();
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
                        "news_id" => $storeNews["id"],
                        "news_tag_id" => $newsTagId
                    );
                    NewsTagsModel::updateOrcreate($insertNewsWithTag);
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
