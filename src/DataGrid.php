<?php

namespace Dcoding;

use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;

/**
 * DataGrid class.
 * Displays Laravel Collection in a grid view.
 * Uses sorting and pagination based on Collection functionality.
 * @todo : add lang replacements in class and template instead of hard code.
 * @todo : move action links to dedicated template and add its name to options.
 * @author Ivan Polyakov <ipaliakou@elinext.com>
 * @package App\Services\DataGrid
 * @version 1.0.0
 */
class DataGrid
{
    /** @var Collection $data */
    private $data;

    /** @var array $slice */
    private $slice;

    /** @var array $columns */
    private $columns;

    /** @var array $options */
    private $options;

    /** @var array $defaultOptions */
    private static $defaultOptions = [
        'template' => 'datagrid',
        'caption' => null,
        'model' => 'Item',
        'controller' => 'item',
        'itemsPerPage' => 15,
        'sort' => ['id'],
        'search' => []
    ];

    /**
     * Sets default options for the grid.
     * @param array $options
     */
    public static function setDefaults(array $options)
    {
        self::$defaultOptions = $options + self::$defaultOptions;
    }

    /**
     * DataGrid constructor.
     * @param Collection $data to be displayed in a grid.
     * @param array $columns of data to be displayed in a grid.
     * @param array $options of the grid instance.
     * @throws \Exception
     */
    public function __construct(Collection $data, array $columns, array $options = [])
    {
        $this->setData($data);
        $this->setColumns($columns);
        $this->setOptions($options);
    }

    /**
     * Sets data to be displayed for the current grid instance.
     * @param Collection $data
     * @throws \Exception
     */
    private function setData(Collection $data)
    {
        if (empty($data)) {
            throw new \Exception('No data provided');
        }

        # Sort The Data
        if ($key = request('sortBy')) {
            if (strtolower(request('sortDirection', 'desc')) == 'desc') {
                $data = collect($data->sortByDesc($key)->values()->all());
            } else {
                $data = collect($data->sortBy($key)->values()->all());
            }
        }

        $this->data = $data;
    }

    /**
     * Sets columns of data to be displayed for the current grid instance.
     * @param array $columns
     * @throws \Exception
     */
    private function setColumns(array $columns)
    {
        if (empty($columns)) {
            throw new \Exception('No columns specified');
        }

        $this->columns = $columns;
    }

    /**
     * Sets options for the current grid instance.
     * @param array $options
     */
    private function setOptions(array $options)
    {
        $this->options = $options + self::$defaultOptions;
    }

    /**
     * Renders DataGrid template.
     * Default template name is `datagrid.blade.php`.
     * To set template name, pass new name to options using `template` key.
     * @return View
     */
    public function render()
    {
        return view($this->options['template'], [
            'columns' => $this->columns,
            'sort' => $this->options['sort'],
            'search' => $this->options['search'],
            'data' => $this->getSlice(),
            'paginator' => $this->getPaginator(),
            'caption' => $this->options['caption'],
            'model' => $this->options['model'],
            'controller' => $this->options['controller']
        ]);
    }

    /**
     * Returns Collection slice for the current page.
     * @return array
     */
    private function getSlice()
    {
        if (is_null($this->slice)) {
            $this->slice = $this->data->slice($this->getOffset(), $this->options['itemsPerPage'])->all();
        }

        return $this->slice;
    }

    /**
     * Returns Collection offset for the current page slice.
     * @return int
     */
    private function getOffset()
    {
        return ($this->getPage() - 1) * $this->options['itemsPerPage'];
    }

    /**
     * Returns current page number.
     * @return int
     */
    private function getPage()
    {
        return abs(request()->get('page', 1));
    }

    /**
     * Returns new Paginator instance for the current grid instance.
     * @return Paginator
     */
    private function getPaginator()
    {
        $items = $this->data->slice($this->getOffset())->all();

        $queryParams = [];
        parse_str(request()->getQueryString(), $queryParams);

        return new Paginator($items, $this->options['itemsPerPage'], $this->getPage(), [
            'path' => url()->current(),
            'query' => $queryParams
        ]);
    }
}
