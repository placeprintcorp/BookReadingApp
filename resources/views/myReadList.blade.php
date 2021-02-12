@extends('layouts.app')

@section('content')
<div id="myReadList" v-cloak>
    
    <div class="row" id="bookResult" style="padding: 0 55px">
        <v-server-table
            class="table default-table table--groups"
            ref="userBooksListing"
            url="/user/my-read-list-data"
            :columns="columns"
            :options="options"
            @filter="filtered"
        >
        
        <div slot="actions" slot-scope="props" class="table--apiKeys__actions-links">

            <a
                :href="'/user/view-book/'+props.row.book_id"
                class="btn-link actions__link d-inline-block">
                
                <i class="fa fa-search actions__icon"></i>View
            </a>

            <a href="#" class="delete_me" data-id=""  role="button" @click.stop.prevent="removeFromReadList(props.row.book_id)"> 
                <i class="fa fa-trash text-inverse m-r-10"></i> Remove
            </a>
        </div>
    </v-server-table>

    </div>   
   
</div>

@endsection
@section('header_styles')

<script src="https://use.fontawesome.com/f21524db91.js"></script>

@endsection
@section('footer_scripts')
<script src="{{ mix('js/myReadList.js') }}"></script>
@endsection