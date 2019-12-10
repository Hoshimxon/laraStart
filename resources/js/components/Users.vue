<template>
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Users</h3>

                        <div class="card-tools">
                            <button class="btn btn-success" @click="newModal">Adda New <i class="fa fa-user-plus"></i></button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Registered At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{user.id}}</td>
                                <td>{{user.name}}</td>
                                <td>{{user.email}}</td>
                                <td>{{user.type | toCapitalize}}</td>
                                <td>{{user.created_at | created}}</td>
                                <td>
                                    <a href="#" @click="editUser(user)">
                                        <i class="fa fa-edit blue"></i>
                                    </a>
                                    /
                                    <a href="#" @click="deleteUser(user.id)">
                                        <i class="fa fa-trash-alt red"></i>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- Modal -->
            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addNewLabel">
                                <span v-if="modalMode === 'create'">Add New</span>
                                <span v-else>Update User</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="modalMode === 'create' ? createUser() : updateUser()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <input v-model="form.name" type="text" name="name" placeholder="Name"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                    <has-error :form="form" field="name"></has-error>
                                </div>
                                <div class="form-group">
                                    <input v-model="form.email" type="email" name="email" placeholder="Email Address"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                    <has-error :form="form" field="email"></has-error>
                                </div>
                                <div class="form-group">
                                <textarea v-model="form.bio" name="bio" id="bio" placeholder="Short bio for user (Optional)"
                                          class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                                    <has-error :form="form" field="bio"></has-error>
                                </div>
                                <div class="form-group">
                                    <select name="type" id="type" v-model="form.type" class="form-control" :class="{'is-invalid': form.errors.has('type')}">
                                        <option value="user">Select User Role</option>
                                        <option value="admin">Admin</option>
                                        <option value="standard">Standard User</option>
                                        <option value="author">Author</option>
                                    </select>
                                    <has-error :form="form" field="type"></has-error>
                                </div>
                                <div class="form-group">
                                    <input v-model="form.password" type="password" name="password"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                    <has-error :form="form" field="password"></has-error>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">
                                    <span v-if="modalMode === 'create'">Create</span>
                                    <span v-else>Update</span>
                                </button>
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
                users: {},
                modalMode: 'create',
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: 'user',
                    bio: '',
                    photo: '',
                })
            }
        },
        created() {
            this.getUsers();
            Fire.$on('afterCreate', () => {
                this.getUsers();
            })
        },
        methods: {
            newModal() {
                this.form.reset();
                this.form.clear();
                this.modalMode = 'create';
                $('#addNew').modal('show');
            },
            editUser(user) {
                this.modalMode = 'edit';
                this.form.clear();
                $('#addNew').modal('show');
                this.form.fill(user);
            },
            updateUser() {
                this.$Progress.start();
                this.form.put('api/user/' + this.form.id)
                    .then(() => {
                        $('#addNew').modal('hide');
                        Swal.fire(
                            'Updated!',
                            'Information has been updated.',
                            'success'
                        );
                        Fire.$emit('afterCreate');
                        his.$Progress.finish();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    })
            },
            getUsers(){
                axios.get("api/user").then(({data}) => (this.users = data.data));
            },
            createUser() {
                this.$Progress.start();
                this.form.post('api/user')
                    .then(()=>{
                        Fire.$emit('afterCreate');
                        $('#addNew').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: 'User created successfully'
                        });
                        this.$Progress.finish();
                    })
                    .catch(()=>{
                        this.$Progress.fail();
                    });
            },
            deleteUser(id) {
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
                        this.form.delete('api/user/' + id);
                        Fire.$emit('afterCreate');
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                    }
                }).catch(() => {
                    Swal("Failed", "There was something wrong.", "warning")
                })
            }
        }
    }
</script>
