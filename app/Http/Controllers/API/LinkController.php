<?php

namespace App\Http\Controllers\API;

use App\Link;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class LinkController extends Controller
{
    public function prepareLinks()
    {
        $links = Link::all();

        $data = $this->tree($links);

        return $data;
    }

    private function tree($elements, $parent_id = null)
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parent_id) {
                $child = $this->tree($elements, $element['id']);
                if ($child) {
                    $element['child'] = $child;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public function getParentLinks()
    {
        $links = Link::all();

        return $links;
    }

    public function index(Request $request)
    {
        $query = Link::query();

        if($request->has('searchfor') && $request->searchfor) {
            $q = $request->searchfor;

            $query->where('title', 'like', "%$q%")
                ->orWhere('url', 'like', "%$q%");
        }

        return $query->orderBy('sort')->get();
    }

    public function store(Request $request)
    {
        $link = new Link();

        $link->title = $request->title;
        $link->url = $request->url;
        $link->parent_id = isset($request->parent_id) && !empty($request->parent_id) ? $request->parent_id : null;

        $link->save();

        return $link;
    }

    public function update(Request $request, $id)
    {
        $link = Link::findOrFail($id);

        $link->title = $request->title;
        $link->url = $request->url;
        $link->parent_id = isset($request->parent_id) && !empty($request->parent_id) ? $request->parent_id : null;

        $link->save();

        return $link;
    }

    public function destroy($id)
    {
        $link = Link::findOrFail($id);

        $link->delete();

        return response()->json(['status' => '200', 'message' => 'link removed successfully.']);
    }

    public function sort(Request $request)
    {
        $links = Link::whereIn('id', $request->ids)->get();

        return $this->sortLinks($links, $request->ids);
    }

    private function sortLinks($links, $ids)
    {
        $order = 1;

        foreach ($ids as $id) {
            $task = $links->where('id', $id)->first();

            $task->sort = $order;
            $task->save();

            $order++;
        }

        return $links->sortBy('sort')->values()->all();
    }
}