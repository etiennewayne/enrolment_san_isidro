<template>
    <div>
        <div class="section">
            <div class="columns is-centered">
                <div class="column is-6">
                    <div class="box">

                        <div class="table-text">BLOCKS</div>

                        <b-field label="Search" label-position="on-border">
                            <b-input type="text"
                                        v-model="search.academic_year" placeholder="Search Academic Year"
                                        @keyup.native.enter="loadAsyncData"/>
                            <p class="control">
                                    <b-tooltip label="Search" type="is-success">
                                <b-button type="is-primary" icon-right="magnify" @click="loadAsyncData"/>
                                    </b-tooltip>
                            </p>
                        </b-field>

                        <div class="buttons is-right mt-3">
                            <b-button @click="openModal" icon-left="plus" class="is-primary is-small">NEW</b-button>
                        </div>

                        <b-table
                            :data="data"
                            :loading="loading"
                            paginated
                            backend-pagination
                            :total="total"
                            :pagination-rounded="true"
                            :per-page="perPage"
                            @page-change="onPageChange"
                            aria-next-label="Next page"
                            aria-previous-label="Previous page"
                            aria-page-label="Page"
                            aria-current-label="Current page"
                            backend-sorting
                            :default-sort-direction="defaultSortDirection"
                            @sort="onSort">

                            <b-table-column field="academic_year_id" label="ID" sortable v-slot="props">
                                {{ props.row.academic_year_id }}
                            </b-table-column>

                            <b-table-column field="academic_year" label="Academic Year" sortable v-slot="props">
                                {{ props.row.academic_year_code }}
                            </b-table-column>

                            <b-table-column field="academic_year_desc" label="Description" sortable v-slot="props">
                                {{ props.row.academic_year_desc }}
                            </b-table-column>

                            <b-table-column field="is_active" label="Active" sortable v-slot="props">
                                <span style="color: green;" v-if="props.row.is_active === 1">
                                    <b-icon icon="check-bold"></b-icon>
                                </span>
                                <span v-else style="color: red;">
                                    <b-icon icon="close-thick"></b-icon>
                                </span>
                                
                            </b-table-column>

                            <b-table-column label="Action" v-slot="props">
                                <div class="is-flex">
                                    <b-tooltip label="Edit" type="is-warning">
                                        <b-button class="button is-small mr-1" tag="a" icon-right="pencil" @click="getData(props.row.academic_year_id)"></b-button>
                                    </b-tooltip>
                                    <b-tooltip label="Delete" type="is-danger">
                                        <b-button class="button is-small mr-1" icon-right="delete" @click="confirmDelete(props.row.academic_year_id)"></b-button>
                                    </b-tooltip>
                                    <b-tooltip label="Active" type="is-info">
                                        <b-button class="button is-small mr-1" icon-right="information" @click="setActive(props.row.academic_year_id)"></b-button>
                                    </b-tooltip>
                                  
                                </div>
                            </b-table-column>
                        </b-table>

                        <div class="columns">
                            <div class="column">
                                <b-field label="Page" label-position="on-border">
                                    <b-select v-model="perPage" @input="setPerPage" class="is-small">
                                        <option value="5">5 per page</option>
                                        <option value="10">10 per page</option>
                                        <option value="15">15 per page</option>
                                        <option value="20">20 per page</option>
                                    </b-select>
                                </b-field>
                            </div>
                        </div>

                    </div>
                </div><!--col -->
            </div><!-- cols -->
        </div><!--section div-->



        <!--modal create-->
        <b-modal v-model="isModalCreate" has-modal-card
                 trap-focus
                 :width="640"
                 aria-role="dialog"
                 aria-label="Modal"
                 aria-modal>

            <form @submit.prevent="submit">
                <div class="modal-card">
                    <header class="modal-card-head">
                        <p class="modal-card-title">Add/Update Academic Year</p>
                        <button
                            type="button"
                            class="delete"
                            @click="isModalCreate = false"/>
                    </header>

                    <section class="modal-card-body">
                        <div class="">
                            <div class="columns">
                                <div class="column">
                                    <b-field label="Academic Year" label-position="on-border"
                                            :type="this.errors.academic_year_code ? 'is-danger':''"
                                            :message="this.errors.academic_year_code ? this.errors.academic_year_code[0] : ''">
                                        <b-input v-model="fields.academic_year_code"
                                                 placeholder="Academic Year" required>
                                        </b-input>
                                    </b-field>
                                </div>
                            </div>
                            <div class="columns">
                                <div class="column">
                                    <b-field label="Description" label-position="on-border"
                                             :type="this.errors.academic_year_desc ? 'is-danger':''"
                                             :message="this.errors.academic_year_desc ? this.errors.academic_year_desc[0] : ''">
                                        <b-input v-model="fields.academic_year_desc"
                                                 placeholder="Academic Year" required>
                                        </b-input>
                                    </b-field>
                                </div>
                            </div>
                        </div>
                    </section>
                    <footer class="modal-card-foot">
                        <button class="button is-primary">Save</button>
                    </footer>
                </div>
            </form><!--close form-->
        </b-modal>
        <!--close modal-->

    </div>
</template>

<script>

export default{
    props: ['propAcademicYears'],
    data() {
        return{
            data: [],
            total: 0,
            loading: false,
            sortField: 'academic_year_id',
            sortOrder: 'desc',
            page: 1,
            perPage: 10,
            defaultSortDirection: 'asc',


            global_id : 0,

            search: {
                academic_year: '',
            },

            isModalCreate: false,
           
            fields: {
                academic_year: '', 
            },
            errors: {},

        }

    },

    methods: {
        /*
        * Load async data
        */
        loadAsyncData() {
            const params = [
                `sort_by=${this.sortField}.${this.sortOrder}`,
                `academic_year=${this.search.academic_year}`,
                `perpage=${this.perPage}`,
                `page=${this.page}`
            ].join('&')

            this.loading = true
            axios.get(`/get-academic-years?${params}`)
                .then(({ data }) => {
                    this.data = [];
                    let currentTotal = data.total
                    if (data.total / this.perPage > 1000) {
                        currentTotal = this.perPage * 1000
                    }

                    this.total = currentTotal
                    data.data.forEach((item) => {
                        //item.release_date = item.release_date ? item.release_date.replace(/-/g, '/') : null
                        this.data.push(item)
                    })
                    this.loading = false
                })
                .catch((error) => {
                    this.data = []
                    this.total = 0
                    this.loading = false
                    throw error
                })
        },
        /*
        * Handle page-change event
        */
        onPageChange(page) {
            this.page = page
            this.loadAsyncData()
        },

        onSort(field, order) {
            this.sortField = field
            this.sortOrder = order
            this.loadAsyncData()
        },

        setPerPage(){
            this.loadAsyncData()
        },

        openModal(){
            this.isModalCreate=true;
            this.clearFields()
        },

        submit: function(){
            if(this.global_id > 0){
                //update
                axios.put('/academic-years/'+this.global_id, this.fields).then(res=>{
                    if(res.data.status === 'updated'){
                        this.$buefy.dialog.alert({
                            title: 'UPDATED!',
                            message: 'Successfully updated.',
                            type: 'is-success',
                            onConfirm: () => {
                                this.loadAsyncData();
                                this.clearFields();
                                this.global_id = 0;
                                this.isModalCreate = false;
                            }
                        })
                    }
                }).catch(err=>{
                    if(err.response.status === 422){
                        this.errors = err.response.data.errors;
                    }
                })
            }else{
                //INSERT HERE
                axios.post('/academic-years', this.fields).then(res=>{
                    if(res.data.status === 'saved'){
                        this.$buefy.dialog.alert({
                            title: 'SAVED!',
                            message: 'Successfully saved.',
                            type: 'is-success',
                            confirmText: 'OK',
                            onConfirm: () => {
                                this.isModalCreate = false;
                                this.loadAsyncData();
                                this.clearFields();
                                this.global_id = 0;
                            }
                        })
                    }
                }).catch(err=>{
                    if(err.response.status === 422){
                        this.errors = err.response.data.errors;
                    }
                });
            }
        },


        //alert box ask for deletion
        confirmDelete(delete_id) {
            this.$buefy.dialog.confirm({
                title: 'DELETE!',
                type: 'is-danger',
                message: 'Are you sure you want to delete this data?',
                cancelText: 'Cancel',
                confirmText: 'Delete',
                onConfirm: () => this.deleteSubmit(delete_id)
            });
        },
        //execute delete after confirming
        deleteSubmit(delete_id) {
            axios.delete('/academic-years/' + delete_id).then(res => {
                this.loadAsyncData();
                this.clearFields()
            }).catch(err => {
                if (err.response.status === 422) {
                    this.errors = err.response.data.errors;
                }
            });
        },

        clearFields(){
            this.global_id = 0;
            this.errors = {};
            this.fields = {
                designation: '', 
            };
        },

        setActive(id){
            axios.post('/academic-year-active/' + id).then(res => {
                this.loadAsyncData();
                this.clearFields()
            })
        },

        //update code here
        getData: function(data_id){
            this.clearFields();
            this.global_id = data_id;
            this.isModalCreate = true;

            //nested axios for getting the address 1 by 1 or request by request
            axios.get('/academic-years/'+data_id).then(res=>{
                this.fields = res.data;
            });
        },

      
    },

    mounted() {
        this.loadAsyncData()
    }

}


</script>


<style scoped>

    .table > tbody > tr {
        /* background-color: blue; */
        transition: background-color 0.5s ease;
    }

    .table > tbody > tr:hover {
        background-color: rgb(233, 233, 233);
    }

</style>
