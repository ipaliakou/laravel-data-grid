@php

    // sorting params.
    $sortBy = request('sortBy');
    $sortDirection = request('sortDirection', 'asc');
    $newSortDirection = ($sortDirection == 'desc') ? 'asc' : 'desc';
    $sortIcon = ($sortDirection == 'desc') ? 'fas fa-sort-alpha-up-alt' : 'fas fa-sort-alpha-down';

    // sorting url.
    $queryParams = [];
    parse_str(request()->getQueryString(), $queryParams);
    unset($queryParams['sortBy'], $queryParams['sortDirection']);
    $queryParams['sortDirection'] = $newSortDirection;
    $sortUrl = url()->current() .'?'. http_build_query($queryParams);

@endphp
<div class="clearfix">
    <div class="float-lg-left mb-4">
        <a class="btn btn-primary" href="{{ url($controller) }}/form">Create new {{ $model }}</a>
    </div>
    <div class="float-lg-right">
        {{ $paginator->links() }} <!-- Pagination -->
    </div>
</div>
<div class="row">
    <div class="col-lg-12">

        <!-- Search form open -->
        @if(!empty($search))
            {{ Form::open([
                'id' => 'search-form',
                'method' => 'post',
                'url' => ($search['url'] ?? url()->current()),
                'files' => false,
            ]) }}
        @endif
        <!-- end -->

        <table class="table table-sm datagrid">
            @if(!empty($caption))
                <caption>{{ $caption }}</caption> <!-- Caption -->
            @endif
            <thead>
                <tr>

                    <!-- Dynamic headers -->
                    @foreach($columns as $column)
                        <th scope="col">
                            @if(in_array($column, $sort))
                                <a href="{{ $sortUrl }}&amp;sortBy={{ $column }}">{{ ucfirst($column) }}</a>
                                @if($sortBy == $column)
                                    <i class="{{ $sortIcon }}"></i>
                                @endif
                            @else
                                {{ ucfirst($column) }}
                            @endif
                        </th>
                    @endforeach
                    <!-- end -->

                    <!-- Static headers -->
                    <th scope="col">Actions</th>
                    <!-- end -->

                </tr>
            </thead>
            <tbody>

                <!-- Search form inputs -->
                @if(!empty($search))
                    <!-- Dynamic columns -->
                    @foreach($columns as $column)
                        <td>
                            @if(in_array($column, $search['columns']))
                                {{ Form::text('search['. $column .']', ($search['values'][$column] ?? ''), [
                                    'class' => 'form-control form-control-sm',
                                ]) }}
                            @endif
                        </td>
                    @endforeach
                    <!-- Static columns -->
                    <td>
                        <button class="btn btn-sm btn-primary" v-on:click="DG_submitSearchForm">Search</button>
                    </td>
                @endif
                <!-- end -->

                <!-- Data rows -->
                @foreach($data as $row)
                    <tr>
                        <!-- Dynamic columns -->
                        @foreach($columns as $column)
                            <td>{{ $row->$column }}</td>
                        @endforeach
                        <!-- Static columns -->
                        <td>
                            <a href="{{ url($controller) }}/{{ $row->id }}"><i class="fas fa-eye"></i></a>
                            <a href="{{ url($controller) }}/form/{{ $row->id }}"><i class="fas fa-edit"></i></a>
                            <a href="{{ url($controller) }}/delete/{{ $row->id }}"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                @endforeach
                <!-- end -->

            </tbody>
        </table>

        <!-- Search form close -->
        @if(!empty($search))
            {{ Form::close() }}
        @endif
        <!-- end -->

    </div>
</div>
