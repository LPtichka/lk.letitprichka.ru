<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;

class LinkPager extends \yii\widgets\LinkPager
{

    public $maxButtonCount = 5;

    public $hideOnSinglePage = false;

    public $options = ['class' => 'pagination pagination-sm'];

    public $disabledPageCssClass = 'hide';

    public $firstPageLabel = true;

    public $lastPageLabel = true;

    public $pageIput = true;

    public $sizeInput = true;

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!isset($config['nextPageLabel'])) {
            $config['nextPageLabel'] = Yii::t('app', 'след');
        }
        if (!isset($config['prevPageLabel'])) {
            $config['prevPageLabel'] = Yii::t('app', 'пред');
        }

        parent::__construct($config);
    }

    /**
     * @return string
     */
    protected function renderPageButtons()
    {
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }

        $buttons     = [];
        $currentPage = $this->pagination->getPage();
        list($beginPage, $endPage) = $this->getPageRange();

        // prev page
        if ($this->prevPageLabel !== false) {
            if (($page = $currentPage - 1) < 0) {
                $page = 0;
            }
            $buttons[] = $this->renderPageButton($this->prevPageLabel, $page, $this->prevPageCssClass, $currentPage <= 0, false);
        }

        // first page
        $firstPageLabel = $this->firstPageLabel === true ? '1' : $this->firstPageLabel;
        if ($firstPageLabel !== false) {
            $buttons[] = $this->renderPageButton($firstPageLabel, 0, $this->firstPageCssClass, 1 > $beginPage, false);
            if (1 < $beginPage) {
                $buttons[] = Html::tag('li', '<span class="text">...</span>');
            }
        }


        // internal pages
        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $buttons[] = $this->renderPageButton($i + 1, $i, null, false, $i === $currentPage);
        }

        // last page
        $lastPageLabel = $this->lastPageLabel === true ? $pageCount : $this->lastPageLabel;
        if ($lastPageLabel !== false) {
            if ($pageCount > $endPage + 2) {
                $buttons[] = Html::tag('li', '<span class="text">...</span>');
            }
            $buttons[] = $this->renderPageButton($lastPageLabel, $pageCount - 1, $this->lastPageCssClass, $pageCount <= $endPage + 1, false);
        }

        // next page
        if ($this->nextPageLabel !== false) {
            if (($page = $currentPage + 1) >= $pageCount - 1) {
                $page = $pageCount - 1;
            }
            $buttons[] = $this->renderPageButton($this->nextPageLabel, $page, $this->nextPageCssClass, $currentPage >= $pageCount - 1, false);
        }

        $pageIput = null;

        if ($this->pageIput !== false) {
            $pageParam = $this->pagination->pageParam;
            if ($this->pageIput == null || $this->pageIput === true) {
                $buttons[] = Html::tag('li', '<i>перейти к странице: </i>' . Html::input('text', $pageParam, Yii::$app->request->get($pageParam), ['id' => 'current-page', 'class' => 'form-control input-sm']));
            } else {
                $buttons[] = $this->pageIput;
            }


            Yii::$app->view->registerJs(<<<JS
                $('#current-page').on('change', function() {
                    var searchParams = new URLSearchParams(location.search);
                    searchParams.set('$pageParam', $(this).val());
                    location.href = location.origin + location.pathname + '?' + searchParams.toString();
                });
JS
            );
        }

        if ($this->sizeInput !== false) {
            $sizeParam   = $this->pagination->pageSizeParam;
            $defaultSize = $this->pagination->defaultPageSize;
            if ($this->sizeInput == null || $this->sizeInput === true) {
                $buttons[] = Html::tag(
                    'li',
                    '<i>кол-во строк в таблице: </i>' . Html::input('text', $sizeParam, Yii::$app->request->get($sizeParam, $defaultSize), ['id' => 'size-page', 'class' => 'form-control input-sm']),
                    ['style' => 'position: absolute; right: 0;']
                );
            } else {
                $buttons[] = $this->sizeInput;
            }

            Yii::$app->view->registerJs(<<<JS
                $('#size-page').on('change', function() {
                    var searchParams = new URLSearchParams(location.search);
                    searchParams.set('$sizeParam', $(this).val());
                    location.href = location.origin + location.pathname + '?' + searchParams.toString();
                });
JS
            );
        }

        return Html::tag('ul', implode("\n", $buttons), $this->options);
    }

    /**
     * @return array
     */
    protected function getPageRange()
    {
        list($beginPage, $endPage) = parent::getPageRange();
        $currentPage = $this->pagination->getPage();
        $pageCount   = $this->pagination->getPageCount();

        if ($this->maxButtonCount - $currentPage > 1 && $pageCount > $this->maxButtonCount) {
            $beginPage = 0;
            $endPage   = $this->maxButtonCount - 1;
        }


        if ($pageCount - $currentPage < $this->maxButtonCount && $pageCount - $this->maxButtonCount > 0) {
            $beginPage = $pageCount - $this->maxButtonCount;
            $endPage   = $pageCount - 1;
        }

        return [$beginPage, $endPage];
    }
}
