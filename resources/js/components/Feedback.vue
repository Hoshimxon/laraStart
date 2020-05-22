<template>
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Feedback</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Company</th>
                                <th>Name</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="feedback in feedbacks.data" :key="feedback.id">
                                <td>{{feedback.id}}</td>
                                <td>{{feedback.company}}</td>
                                <td>{{feedback.name}}</td>
                                <td>{{feedback.created_at | created}}</td>
                                <td>
                                    <a href="#" @click="detail(feedback)">
                                        <i class="fa fa-edit blue"></i>
                                    </a>
                                    /
                                    <a href="#" @click="deletePost(feedback.id)">
                                        <i class="fa fa-trash-alt red"></i>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <pagination :data="feedbacks" @pagination-change-page="getSettings"></pagination>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <!-- Modal -->
            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addNewLabel">
                                <span>Detail</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                            <form>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Company</label>
                                        <input v-model="form.company" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input v-model="form.name" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input v-model="form.phone" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input v-model="form.email" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Ip</label>
                                        <input v-model="form.ip" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                feedbacks: {},
                modalMode: 'create',
                form: {
                    company: '',
                    name: '',
                    phone: '',
                    email: '',
                    ip: ''
                },
            }
        },
        created() {
            this.getSettings();
            Fire.$on('afterCreate', () => {
                this.getSettings();
            });
        },
        methods: {
            detail(feedback) {
                this.form = feedback;
                $('#addNew').modal('show');
            },
            getSettings(page = 1){
                axios.get(this.$http + 'feedback?page=' + page).then(({data}) => (this.feedbacks = data));
            },
            deletePost(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        axios.delete(this.$http + 'feedback/' + id);
                        Fire.$emit('afterCreate');
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                    }
                }).catch(() => {
                    Swal.fire("Failed", "There was something wrong.", "error")
                })
            }
        }
    }
</script>
