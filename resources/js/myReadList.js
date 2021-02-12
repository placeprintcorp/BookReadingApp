import { toastedMessages } from './components/mixins/toastedMessages'
import { ServerTable, Event  } from 'vue-tables-2'
import VuejsDialog from 'vuejs-dialog'

// include the default style
import 'vuejs-dialog/dist/vuejs-dialog.min.css';

Vue.use(ServerTable)
Vue.use(VuejsDialog)

var MyListScreen = new Vue({
    el: '#myReadList',
    http    :   {
        headers: {
            'X-CSRF-Token': $('meta[name=_token]').attr('content')
        }
    },
    mixins: [ toastedMessages ],
    data(){
        return {
            columns: [
                'book_img_url',
                'book_title',
                'authors',
                'actions',
            ],
            options: {
                highlightMatches: true,
                texts: {
                    filterPlaceholder: 'Search Book...',
                    noResults: 'No book match your search',
                },
                sortIcon: {
                    base: '',
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down',
                },
                sortable: [
                    'book_title',
                ],
                columnsClasses: {
                    book_img_url: 'table--books__book_image',
                    book_title: 'table--books__book_title',
                    authors: 'table--books__authors',
                    actions: 'table--books__actions',
                },
            },

            books : [],
            filterValue: '',
        }
    },
    beforeCreate(){
       
    },
    methods : {
        onPaginationData (paginationData) {
            this.$refs.pagination.setPaginationData(paginationData)
        },
        filtered(value) {
            this.filterValue = value;
        },
        refresh() {
            this.$refs.userBooksListing.refresh()
        },
        
        removeFromReadList(bookeId) {
            var baseUrl = $('#baseUrl').val();
            this.$dialog.confirm('Are you sure you want to remove book from read list?')
                .then(() => {
                    axios.delete(baseUrl+`/user/remove-book/${bookeId}`)
                        .then(response => {
                            this.refresh()
                            this.toastedSuccessMessage(response.data.message)
                        })
                        .catch(error => {
                            this.toastedErrorMessage(error.response.data.message)
                        })
                })
        },
    }
});