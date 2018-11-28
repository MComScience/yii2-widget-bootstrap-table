<?php
/**
 * Created by PhpStorm.
 * User: Tanakorn
 * Date: 13/11/2561
 * Time: 19:46
 */
namespace mcomscience\bstable;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use mcomscience\datatables\DataTables;

class BootstrapTable extends Widget
{
    const TYPE_DEFAULT = 'default';

    const TYPE_PRIMARY = 'primary';

    const TYPE_INFO = 'info';

    const TYPE_DANGER = 'danger';

    const TYPE_WARNING = 'warning';

    const TYPE_SUCCESS = 'success';

    const TYPE_ACTIVE = 'active';

    public $options = [];

    public $beforeHeader = [];

    public $afterHeader = [];

    public $beforeFooter = [];

    public $afterFooter = [];

    public $layout = "{items}";

    public $panel = [];

    public $panelPrefix = 'panel panel-';

    public $panelTemplate = <<< HTML
<div class="{prefix}{type}">
    {panelHeading}
    {panelBefore}
    {items}
    {panelAfter}
    {panelFooter}
</div>
HTML;

    public $panelHeadingTemplate = <<< HTML
    <div class="pull-right">
        {tools}
    </div>
    <h3 class="panel-title">
        {heading}
    </h3>
    <div class="clearfix"></div>
HTML;

    public $panelFooterTemplate = <<< HTML
    <span class="pull-right">
        {footer-right}
    </span>
        {footer-left}
    <div class="clearfix"></div>
HTML;

    public $panelBeforeTemplate = <<< HTML
    {toolbarContainer}
    {before}
    <div class="clearfix"></div>
HTML;

    public $panelAfterTemplate = '{after}';

    public $tableOptions = ['width' => '100%'];

    public $hover = true;

    public $striped = true;

    public $bordered = false;

    public $condensed = false;

    public $caption;

    public $captionOptions = [];

    public $columns = [];

    public $footerOptions = [];

    public $showHeader = true;

    public $showFooter = false;

    public $theadOptions = [];

    public $toolbar = [];

    public $toolbarContainerOptions = ['class' => 'btn-toolbar toolbar-container pull-right'];

    public $datatableOptions = [];

    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    public function run()
    {
        $this->initBootstrapStyle();
        $this->renderPanel();
        $this->replaceLayoutTokens([
            '{toolbarContainer}' => $this->renderToolbarContainer(),
            '{toolbar}' => $this->renderToolbar(),
        ]);
        $this->renderItems();
        echo $this->layout;
        $this->renderDataTables();
    }

    protected function renderPanel()
    {
        if (!is_array($this->panel) || empty($this->panel)) {
            return;
        }
        $type = ArrayHelper::getValue($this->panel, 'type', 'default');
        $heading = ArrayHelper::getValue($this->panel, 'heading', '');
        $tools = ArrayHelper::getValue($this->panel, 'tools', '');
        $footerleft = ArrayHelper::getValue($this->panel, 'footer-left', '');
        $footerright = ArrayHelper::getValue($this->panel, 'footer-right', '');
        $before = ArrayHelper::getValue($this->panel, 'before', '');
        $after = ArrayHelper::getValue($this->panel, 'after', '');
        $headingOptions = ArrayHelper::getValue($this->panel, 'headingOptions', []);
        $footerOptions = ArrayHelper::getValue($this->panel, 'footerOptions', []);
        $beforeOptions = ArrayHelper::getValue($this->panel, 'beforeOptions', []);
        $afterOptions = ArrayHelper::getValue($this->panel, 'afterOptions', []);
        $panelHeading = '';
        $panelBefore = '';
        $panelAfter = '';
        $panelFooter = '';
        if ($heading !== false || $tools !== false) {
            static::initCss($headingOptions, 'panel-heading');
            $content = strtr($this->panelHeadingTemplate, ['{heading}' => $heading,'{tools}' => $tools]);
            $panelHeading = Html::tag('div', $content, $headingOptions);
        }
        if ($footerright !== false || $footerleft !== false) {
            static::initCss($footerOptions, 'panel-footer');
            $content = strtr($this->panelFooterTemplate, ['{footer-right}' => $footerright,'{footer-left}' => $footerleft]);
            $panelFooter = Html::tag('div', $content, $footerOptions);
        }
        if ($before !== false) {
            static::initCss($beforeOptions, 'panel-before');
            $content = strtr($this->panelBeforeTemplate, ['{before}' => $before]);
            $panelBefore = Html::tag('div', $content, $beforeOptions);
        }
        if ($after !== false) {
            static::initCss($afterOptions, 'panel-after');
            $content = strtr($this->panelAfterTemplate, ['{after}' => $after]);
            $panelAfter = Html::tag('div', $content, $afterOptions);
        }
        $this->layout = strtr(
            $this->panelTemplate,
            [
                '{panelHeading}' => $panelHeading,
                '{prefix}' => $this->panelPrefix,
                '{type}' => $type,
                '{panelFooter}' => $panelFooter,
                '{panelBefore}' => $panelBefore,
                '{panelAfter}' => $panelAfter,
            ]
        );
    }

    protected static function initCss(&$options, $css)
    {
        if (!isset($options['class'])) {
            $options['class'] = $css;
        }
    }

    public function renderItems()
    {
        $caption = $this->renderCaption();
        $tableHeader = $this->showHeader ? $this->renderTableHeader() : false;
        $tableBody = $this->renderTableBody();
        $tableFooter = $this->showFooter ? $this->renderTableFooter() : false;
        $content = array_filter([
            $caption,
            $tableHeader,
            $tableFooter,
            $tableBody,
        ]);
        $id = ArrayHelper::getValue($this->tableOptions, 'id', false);
        if(!$id){
            $this->tableOptions['id'] = $this->getId();
        }
        if (!is_array($this->panel) || empty($this->panel)) {
            $this->layout = strtr(
                $this->layout,
                [
                    '{items}' => Html::tag('table', implode("\n", $content), $this->tableOptions),
                ]
            );
        }else{
            $this->layout = strtr(
                $this->layout,
                [
                    '{items}' => Html::tag('div',Html::tag('table', implode("\n", $content), $this->tableOptions),['class' => 'panel-body']),
                ]
            );
        }
    }

    public function renderCaption()
    {
        if (!empty($this->caption)) {
            return Html::tag('caption', $this->caption, $this->captionOptions);
        }

        return false;
    }

    public function renderTableHeader()
    {
        return Html::beginTag('thead', $this->theadOptions) . "\n".
            $this->generateRows($this->beforeHeader) . "\n" .
            $this->generateRows($this->afterHeader) . "\n" .
            Html::endTag('thead');
    }

    protected function generateRows($data)
    {
        if (empty($data)) {
            return '';
        }
        if (is_string($data)) {
            return $data;
        }
        $rows = '';
        if (is_array($data)) {
            foreach ($data as $row) {
                if (empty($row['columns'])) {
                    continue;
                }
                $rowOptions = ArrayHelper::getValue($row, 'options', []);
                $rows .= Html::beginTag('tr', $rowOptions);
                foreach ($row['columns'] as $col) {
                    $colOptions = ArrayHelper::getValue($col, 'options', []);
                    $colContent = ArrayHelper::getValue($col, 'content', '');
                    $tag = ArrayHelper::getValue($col, 'tag', 'th');
                    $rows .= "\t" . Html::tag($tag, $colContent, $colOptions) . "\n";
                }
                $rows .= Html::endTag('tr') . "\n";
            }
        }
        return $rows;
    }

    public function renderTableBody()
    {
        if (count($this->columns) == 0) {
            return '<tbody></tbody>';
        }
        return  Html::beginTag('tbody', []) . "\n".
            $this->generateRowsData($this->columns) . "\n".
            Html::endTag('tbody');
    }

    public function renderTableFooter()
    {
        return Html::beginTag('tfoot', $this->footerOptions) . "\n".
            $this->generateRows($this->beforeFooter) . "\n" .
            $this->generateRows($this->afterFooter) . "\n" .
            Html::endTag('tfoot');
    }

    protected function renderToolbarContainer()
    {
        $tag = ArrayHelper::remove($this->toolbarContainerOptions, 'tag', 'div');
        return Html::tag($tag, $this->renderToolbar(), $this->toolbarContainerOptions);
    }

    protected function renderToolbar()
    {
        if (empty($this->toolbar) || (!is_string($this->toolbar) && !is_array($this->toolbar))) {
            return '';
        }
        if (is_string($this->toolbar)) {
            return $this->toolbar;
        }
        $toolbar = '';
        foreach ($this->toolbar as $item) {
            if (is_array($item)) {
                $content = ArrayHelper::getValue($item, 'content', '');
                $options = ArrayHelper::getValue($item, 'options', []);
                static::initCss($options, 'btn-group');
                $toolbar .= Html::tag('div', $content, $options);
            } else {
                $toolbar .= "\n{$item}";
            }
        }
        return $toolbar;
    }

    protected function replaceLayoutTokens($pairs)
    {
        foreach ($pairs as $token => $replace) {
            if (strpos($this->layout, $token) !== false) {
                $this->layout = str_replace($token, $replace, $this->layout);
            }
        }
    }

    protected function generateRowsData($data)
    {
        if (empty($data)) {
            return '';
        }
        if (is_string($data)) {
            return $data;
        }
        $rows = '';
        if (is_array($data)) {
            foreach ($data as $row) {
                $rowOptions = ArrayHelper::getValue($row, 'options', []);
                $rows .= Html::beginTag('tr', $rowOptions);
                foreach ($row as $col) {
                    $colOptions = ArrayHelper::getValue($col, 'options', []);
                    $colContent = ArrayHelper::getValue($col, 'content', '');
                    $tag = ArrayHelper::getValue($col, 'tag', 'td');
                    $rows .= "\t" . Html::tag($tag, $colContent, $colOptions) . "\n";
                }
                $rows .= Html::endTag('tr') . "\n";
            }
        }
        return $rows;
    }

    protected function renderDataTables(){
        if($this->datatableOptions){
            $this->datatableOptions['options'] = ['id' => ArrayHelper::getValue($this->tableOptions, 'id', $this->getId())];
            echo DataTables::widget($this->datatableOptions);
        }
    }

    protected function initBootstrapStyle()
    {
        Html::addCssClass($this->tableOptions, 'bs-table');
        Html::addCssClass($this->tableOptions, 'table');
        if ($this->hover) {
            Html::addCssClass($this->tableOptions, 'table-hover');
        }
        if ($this->bordered) {
            Html::addCssClass($this->tableOptions, 'table-bordered');
        }
        if ($this->striped) {
            Html::addCssClass($this->tableOptions, 'table-striped');
        }
        if ($this->condensed) {
            Html::addCssClass($this->tableOptions, 'table-condensed');
        }
    }
}
########## Example ##########
/*
echo BootstrapTable::widget([
    'tableOptions' => ['class' => 'table table-hover table-striped','id' => 'tb-ticket'],
    'panel' => [
        'type' => Table::TYPE_DEFAULT,
        'heading' => Html::tag('h3', Icon::show('list').' บัตรคิว', ['class' => 'panel-title']),
        'before' => '',
        'after' => false,
        'footer-left' => false,
        'footer-right' => false,
    ],
    'toolbar' => [
        [
            'content'=> Html::a(Icon::show('plus') . ' เพิ่มรายการ', ['/app/setting/create-ticket'], ['class' => 'btn btn-success btn-sm']),
        ],
    ],
    'beforeHeader' => [
        [
            'columns' => [
                ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                ['content' => 'ชื่อ รพ. ไทย', 'options' => ['style' => 'text-align: center;']],
                ['content' => 'ชื่อ รพ. อังกฤษ','options' => ['style' => 'text-align: center;']],
                ['content' => 'รหัสโค้ด', 'options' => ['style' => 'text-align: center;']],
                ['content' => 'สถานะการใช้งาน', 'options' => ['style' => 'text-align: center;']],
                ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;']],
            ],
        ],
    ],
    'columns' => [
        'columns' => [
            ['content' => 'A', 'options' => ['style' => 'text-align: center;width: 35px;']],
            ['content' => 'B', 'options' => ['style' => 'text-align: center;']],
            ['content' => 'C','options' => ['style' => 'text-align: center;']],
            ['content' => 'D', 'options' => ['style' => 'text-align: center;']],
            ['content' => 'E', 'options' => ['style' => 'text-align: center;']],
            ['content' => 'F', 'options' => ['style' => 'text-align: center;']],
        ],
    ],
    'datatableOptions' => [
        "clientOptions" => [
            "responsive" => true,
            "language" => [
            ],
            "autoWidth" => false,
            "deferRender" => true,
            "drawCallback" => new JsExpression('function ( settings ) {
                dtFunc.initConfirm("#tb-ticket");
            }'),
        ],
        'clientEvents' => [
            'error.dt' => 'function ( e, settings, techNote, message ){
                e.preventDefault();
                swal({title: \'Error...!\',html: \'<small>\'+message+\'</small>\',type: \'error\',});
            }'
        ]
    ],
]);
*/