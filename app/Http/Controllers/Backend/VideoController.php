<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ContentTagsModel;
use App\Models\JobNotifFirebaseModel;
use App\Models\TagsModel;
use App\Models\VideoModel;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class VideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('backend.video.index', []);
    }

    public function getDatatable(Request $request)
    {
        if (request()->ajax()) {
            $requestData = $request->input();
            $listVideos = VideoModel::select(
                'video_id',
                'category_id',
                'title',
                'slug',
                'content',
                'image',
                'status',
                'created_by'
            );
            // dd(VideoModel::with('videoTags')->get()->toArray());
            return DataTables::of($listVideos)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $result = isset($row['status']) && $row['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                    return $result;
                })
                ->addColumn('video_category_name', function ($row) {
                    $result = isset($row->category) ? $row->category->name : null;
                    return $result;
                })
                ->addColumn('tag', function ($row) {
                    $result = null !== $row->pivotVideoTags->pluck("name") && $row->pivotVideoTags->pluck("name") ? implode(", ", $row->pivotVideoTags->pluck("name")->toArray()) : NULL;
                    return $result;
                })
                ->addColumn('action', function ($row) {
                    $result = "";
                    $result .= '<button type="button" class="btn btn-warning btn-xs m-1 btnView" data-id="' . $row['video_id'] . '">View</button>';
                    $result .= '<button type="button" class="btn btn-danger btn-xs m-1 btnDelete" data-id="' . $row['video_id'] . '">Delete</button>';
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
                $videoId = isset($requestData["id"]) && $requestData["id"] ? $requestData["id"] : null;
                $isDisabled = false;
                $data = NULL;
                if ($videoId) {
                    $isDisabled = true;
                    $data = VideoModel::findOrFail($videoId);
                    $videoCategoryName = isset($data->category) ? $data->category->name : null;
                    $videoTags = $data->pivotVideoTags->pluck("name", "id")->toArray();
                    $data = $data->toArray();
                    $data['video_category_name'] = $videoCategoryName;
                    $data['array_tags'] = $videoTags;
                }
                $throwData = [
                    "data" => $data,
                    "isDisabled" => $isDisabled
                ];
                return view('backend/video/modalFormAdd', $throwData);
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
            if (!isset($requestData["videoTitle"]) || !$requestData["videoTitle"]) {
                throw new Exception("News Title is Empty!", 1);
            }
            if (!isset($requestData["videoCategory"]) || !$requestData["videoCategory"]) {
                throw new Exception("Category Video is Empty!", 1);
            }
            $videoId = isset($requestData["videoId"]) && $requestData["videoId"] ? $requestData["videoId"] : NULL;
            $videoCategory = isset($requestData["videoCategory"]) && $requestData["videoCategory"] ? $requestData["videoCategory"] : NULL;
            $title = isset($requestData["videoTitle"]) && $requestData["videoTitle"] ? trim($requestData["videoTitle"]) : NULL;
            $link = isset($requestData["videoLink"]) && $requestData["videoLink"] ? trim($requestData["videoLink"]) : NULL;
            $content = isset($requestData["videoContent"]) && $requestData["videoContent"] ? trim($requestData["videoContent"]) : NULL;
            $image = isset($requestData["videoImage"]) && $requestData["videoImage"] ? trim($requestData["videoImage"]) : NULL;
            $status = isset($requestData["videoStatus"]) && $requestData["videoStatus"] ? $requestData["videoStatus"] : 0;
            $tags = isset($requestData["videoTags"]) && $requestData["videoTags"] ? $requestData["videoTags"] : 0;
            $dataStore = array(
                "category_id" => $videoCategory,
                "title" => $title,
                "link" => $link,
                "slug" => Str::slug($title),
                "content" => $content,
                "image" => $image,
                "status" => $status
            );
            if (!$videoId) {
                $checkDataExist = VideoModel::where("title", $title)->count();
                if ($checkDataExist != 0) {
                    throw new Exception("Video Already Exist!", 1);
                }
            }
            $storeVideo = VideoModel::updateOrCreate(
                [
                    "video_id" => $videoId
                ],
                $dataStore
            );
            if (!$storeVideo) {
                throw new Exception("Error Store Data!", 1);
            }

            //delete all news tag where not in update tag in table news_with_tag
            ContentTagsModel::where("content_type", 'video')->where("content_id", $storeVideo["video_id"])->delete();
            if (is_array($tags)) {
                $arrayVideoTagId = array();
                foreach ($tags as $rowVideoTag) {
                    $getVideoTag = TagsModel::where("name", strtolower($rowVideoTag))->first();
                    if (!$getVideoTag) {
                        $createdVideoTag = TagsModel::create([
                            "name" => strtolower($rowVideoTag)
                        ]);
                    }
                    $videoTagId = isset($getVideoTag["id"]) ? (int)$getVideoTag["id"] : (int)$createdVideoTag->id;
                    $arrayVideoTagId[] = $videoTagId;
                    $insertVideoWithTag = array(
                        "content_type" => "video",
                        "content_id" => $storeVideo["video_id"],
                        "tag_id" => $videoTagId
                    );
                    ContentTagsModel::updateOrcreate($insertVideoWithTag);
                }
            }

            if ($storeVideo->wasRecentlyCreated) {
                // updateOrCreate performed create
                JobNotifFirebaseModel::create(array(
                    "notif_type" => "videos",
                    "client_key" => "eukHxDWBS3Ws1dq0NU7rAl:APA91bFOQ6zAstOMzATWm4FK1emwY_4DOxPJ33mdUectTpXEoJG2dHGNOBqcmfOnk0rN9YwgXZV8x15xp6Fq4kkmqeTl65ENiuGC1gpvHHOG5hl37P0pR2Db5JRq6hDtAw5ybpBoRgCj",
                    "notif_title" => $title,
                    "notif_body" => substr($content, 0, 100),
                    "notif_img_url" => "https://img.youtube.com/vi/" . $dataStore["link"] . "/maxresdefault.jpg",
                    "notif_url" => "https://www.youtube.com/watch?v=" . $dataStore["link"],
                    "status" => 0
                ));
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
            $videoId = isset($requestData["id"]) && $requestData["id"] ? $requestData["id"] : NULL;
            if (!$videoId) {
                throw new Exception("Video Id is Empty!", 1);
            }
            VideoModel::findOrFail($videoId)->delete();
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
