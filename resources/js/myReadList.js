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
                    book_title: 'table--apiKeys__book_title',
                    authors: 'table--apiKeys__authors',
                    actions: 'table--apiKeys__actions',
                },
            },

            books : [],
            filterValue: '',
        }
    },
    beforeCreate(){
       
    },
    methods : {
        filtered(value) {
            this.filterValue = value;
        },
        refresh() {
            this.$refs.userBooksListing.refresh()
        },
        
        removeFromReadList(bookeId) {
            this.$dialog.confirm('Are you sure you want to remove book from read list?')
                .then(() => {
                    axios.delete(`/user/remove-book/${bookeId}`)
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