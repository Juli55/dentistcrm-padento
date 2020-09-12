<?php
/**
 * Created by PhpStorm.
 * User: amer
 * Date: 09.01.18
 * Time: 11:04
 */

namespace App\Services;


use Illuminate\Pagination\Paginator;

class Pagination extends Paginator
{
    /**
     * Override parent's class CheckForMorePages method.
     * For some reason slice starts at index 0 on parent,
     * so the paginator always returns starting items.
     *
     * @return void
     */
    protected function checkForMorePages()
    {
        $this->hasMore = count($this->items) > ($this->perPage * $this->currentPage());
        $this->total=count($this->items);
        $this->items = $this->items->slice($this->firstItem() -1, $this->perPage);
    }
    public function   toArray()
{
    return [
        'per_page' => $this->perPage(), 'current_page' => $this->currentPage(),
        'next_page_url' => $this->nextPageUrl(), 'prev_page_url' => $this->previousPageUrl(),
        'from' => $this->firstItem(), 'to' => $this->lastItem(),
        'data' => $this->items->toArray(),
        'last_page'=>ceil($this->total/$this->perPage)
    ];
}
}