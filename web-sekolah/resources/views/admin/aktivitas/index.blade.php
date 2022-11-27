@extends('layouts.app')

@section('content')

<div class="block-content"> 
    <table class="table table-borderiess table-striped table-vcenter font-size-sm">
        <tbody>
            @foreach ($activity_log as $item) 
                <tr>
                    <td class="font-w600 text-center" style="width: 100px;">
                        <span class="badge badge-success">{{ $item->user->name }}</span>
                    </td>
                    <td class="d-none d-sm-table-cell"> 
                        <span class="badge badge-success">{{ $item->description }}</span>
                    </td>
                    <td>
                       <span class="badge badge-success">{{ Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</span> 
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@stop