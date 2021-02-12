@extends('layouts.app')

@section('content')
<div id="myReadList" v-cloak>
    
    <div class="row" id="bookResult" style="padding: 0 55px">
        <v-server-table
            class="table default-table table--groups"
            ref="userBooksListing"
            url="{{config('app.url')}}/user/my-read-list-data"
            :columns="columns"
            :options="options"
            @filter="filtered"
        >
        <div slot="book_img_url" slot-scope="props" class="table--books__book_image-links">
            <img :src="props.row.book_img_url" alt="" width="60" height="60" style="
            margin-left: 25px;
        ">
        </div>

        <div slot="actions" slot-scope="props" class="table--books__actions-links">

            <a
                :href="'{{config('app.url')}}/user/view-book/'+props.row.book_id"
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
<script src="{{ url('js/myReadList.js') }}"></script>
@endsection