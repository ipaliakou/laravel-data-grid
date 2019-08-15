# laravel-data-grid
Simple data grid for Laravel collections.

[Packagist](https://packagist.org/packages/dcoding/laravel-data-grid)

## Installation

```
composer require dcoding/laravel-data-grid
```

## Usage

You need to include some elements into your app before start using it.
Please follow points listed below to do so.

1) Add css on a page. You can either copy whole file into `public/css` directory and include it in layout or copy its content into your own file. Also you can ingnore it in case of specific theme. File path in a package is `src/datagrid.css`.

2) Copy blade template from the package `src/datagrid.blade.php` into view root directory `resources/views`. In case of specific theme feel free to adjust the template.

3) In controller you want to use grid, add namespace
```use Dcoding\DataGrid;```

4) In action you want to use grid, add following fragments :

*(optional)* Clear search result which is stored in session :
```
if ($request->isMethod('get') && empty($request->getQueryString())) {
    $request->session()->forget('search');
}
```

*(optional)* Process search request if searching enabled :
```
$search = $request->get('search', session('search'));
if (!empty($search)) {
    session(['search' => $search]);
    $qb = DB::table('{{TABLE_NAME}}');
    foreach ($search as $_field => $_value) {
        if (!empty($_value)) {
            $qb->where($_field, 'like', '%'. $_value .'%');
        }
    }

    $collection = $qb->get();
} else {
    $collection = {{MODEL_NAME}}::all();
}
```

*(required)* Create new Datagrid instance :
```
$dataGrid = new DataGrid($collection, columns, $options);
```

*(required)* Pass DataGrid instance to view :
```
'dataGrid' => $dataGrid
```

*(required)* Render DataGrid instance in a view :
```
{!! $dataGrid->render() !!}
```

## Columns

Currently, you can pass only a list of model fields you want to display columns for like `['id', 'title', 'description']`.

## Options

```
[
    'caption' => 'List of items',
    'model' => 'User',
    'controller' => 'user',
    'itemsPerPage' => 15,
    'sort' => ['id', 'title'],
    'search' => [
        'url' => url()->route('mySearch'), // if not present, current url will be used.
        'columns' => ['id', 'title', 'description'],
        'values' => $search // from search fragment above.
    ]
]
```
