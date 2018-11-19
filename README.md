<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii2 Extension</h1>
    <br>
</p>

## Requirements

The minimum requirement is that your Web server supports PHP 7.0 or >.

# yii2-widget-bootstrap-table
Bootstrap Tables widget for Yii2 framework

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

```
composer require m-comscience/yii2-widget-bootstrap-table '@dev'
```

## Usage

```php
use mcomscience\bstable\BootstrapTable;

echo BootstrapTable::widget([
    'tableOptions' => ['id' => 'tb-example'],
    'hover' => true, // Defaults to true
    'bordered' => true, // Defaults to false
    'striped' => true, // Defaults to true
    'condensed' => true, // Defaults to true
    'caption' => 'Bootstrap Tables widget for Yii2 framework',
    'captionOptions' => [],
    'panel' => [
        'type' => BootstrapTable::TYPE_DEFAULT,
        'heading' => Html::tag('h3', 'Panel Title', ['class' => 'panel-title']),
        'before' => '',
        'after' => false,
        'footer-left' => false,
        'footer-right' => false,
    ],
    'toolbar' => [
        [
            'content' => Html::a('Add',  ['/app/settings/create'], ['class' => 'btn btn-success']),
            'options' => [],
        ],
    ],
    'beforeHeader' => [
        [
            'columns' => [
                ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                ['content' => 'ID', 'options' => ['style' => 'text-align: center;']],
                ['content' => 'Title','options' => ['style' => 'text-align: center;']],
                ['content' => 'Name', 'options' => ['style' => 'text-align: center;']],
                ['content' => 'Status', 'options' => ['style' => 'text-align: center;']],
                ['content' => 'Actions', 'options' => ['style' => 'text-align: center;']],
            ],
        ],
    ],
    // 'afterHeader' => [],
    'showFooter' => true,
    'beforeFooter' => [
        [
            'columns' => [
                ['content' => 'Summary', 'options' => ['colspan' => 4]],
                ['content' => '', 'options' => ['style' => 'text-align: right;']],
                ['content' => '','options' => ['style' => 'text-align: center;']],
            ],
        ],
    ],
    // 'afterFooter' => [],
    'columns' => [
        'columns' => [
            ['content' => '1', 'options' => ['style' => 'text-align: center;width: 35px;']],
            ['content' => '100123', 'options' => ['style' => 'text-align: center;']],
            ['content' => 'Bootstrap','options' => ['style' => 'text-align: center;']],
            ['content' => 'Name', 'options' => ['style' => 'text-align: center;']],
            ['content' => 'Active', 'options' => ['style' => 'text-align: center;']],
            ['content' => Html::a('Delete',['delete','id' => $model['id']],['class' => 'btn btn-sm btn-danger']), 'options' => ['style' => 'text-align: center;']],
        ],
    ],
]);
```

Usage Datatables plug-in jQuery Javascript library
<a href="https://github.com/MComScience/yii2-widget-datatables" target="_blank">
      Datatables Widget
</a>

```php
echo BootstrapTable::widget([
    'tableOptions' => ['class' => 'table table-hover table-striped','id' => 'tb-example'],
    // ... options
    'datatableOptions' => [
        "clientOptions" => [
            "responsive" => true,
            "autoWidth" => false,
            "deferRender" => true,
        ],
        'clientEvents' => [
            'error.dt' => 'function ( e, settings, techNote, message ){
                e.preventDefault();
                console.error(message);
            }'
        ]
    ],
]);
```
