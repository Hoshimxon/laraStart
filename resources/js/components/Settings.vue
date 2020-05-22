<template>
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Settings</h3>
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
                                <th>Title {{this.$language}}</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="post in settings.data" :key="post.id">
                                <td>{{post.id}}</td>
                                <td>{{post.title}}</td>
                                <td>{{post.created_at | created}}</td>
                                <td>
                                    <a href="#" @click="editPost(post)">
                                        <i class="fa fa-edit blue"></i>
                                    </a>
                                    /
                                    <a href="#" @click="deletePost(post.id)">
                                        <i class="fa fa-trash-alt red"></i>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <pagination :data="settings" @pagination-change-page="getSettings"></pagination>
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
                                <span v-if="modalMode === 'create'">Add New</span>
                                <span v-else>Update post</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <ValidationObserver
                                ref="validationObserver"
                                v-slot="{ handleSubmit }"
                        >
                            <form @submit.prevent="modalMode === 'create' ? handleSubmit(createPost) : handleSubmit(updatePost)">
                                <div class="modal-body">
                                    <template v-for="translation in form.translations">
                                        <div class="form-group">
                                            <label> Title {{translation.locale}} </label>
                                            <validation-provider rules="required|max:255" v-slot="{ errors }" :vid="'translations.' + translation.locale + '.title'">
                                                <input v-model="translation.title" :name="'title ' + translation.locale" class="form-control">
                                                <span class="validation-info">{{ errors[0] }}</span>
                                            </validation-provider>
                                        </div>
                                        <div class="form-group">
                                            <label>Description {{translation.locale}}</label>
                                            <textarea v-model="translation.description" class="form-control"></textarea>
                                        </div>
                                    </template>
                                    <div class="form-group">
                                        <label>Select status</label>
                                        <select v-model="form.is_active" class="form-control">
                                            <option :value="true">Active</option>
                                            <option :value="false">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="file">Image</label>
                                        <div class="input-group">
                                            <input type="file" id="file" accept="image/*" @change="selectFile">
                                        </div>
                                    </div>
                                    <div v-if="form.image_url" class="form-group">
                                        <label>Current image</label>
                                        <div class="input-group post-images-group">
                                            <img class="img-circle" width="100px" :src="form.image_url" alt="Setting image">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">
                                        <span v-if="modalMode === 'create'" >Create</span>
                                        <span v-else>Update</span>
                                    </button>
                                </div>
                            </form>
                        </ValidationObserver>
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
                settings: {},
                modalMode: 'create',
                form: {
                    position: 0,
                    translations: [],
                    is_active: true,
                    internal_link: '',
                    external_link: '',
                    image_url: null,
                },
                file: null
            }
        },
        created() {
            this.getSettings();
            Fire.$on('afterCreate', () => {
                this.getSettings();
            });
            this.setTranslations();
        },
        methods: {
            selectFile(e) {
                this.file = e.target.files[0];
            },
            setTranslations() {
                this.$translations.forEach(item => {
                    this.form.translations.push({
                        title: '',
                        description: '',
                        locale: item
                    });
                });
            },
            newModal() {
                this.modalMode = 'create';
                this.form.translations = [];
                this.setTranslations();
                this.form.is_active = true;
                $('#addNew').modal('show');
            },
            editPost(post) {
                this.form = post;
                this.modalMode = 'edit';
                $('#addNew').modal('show');
            },
            updatePost() {
                this.$Progress.start();
                const data = new FormData();
                data.append('translations', JSON.stringify(this.form.translations));
                this.form.is_active === false ? data.append('is_active', 0) : '';

                if(this.file !== null) {
                    data.append('file', this.file);
                }

                data.append('_method', 'put');
                axios.post(this.$http + 'settings/' + this.form.id, data, {headers: {'Content-Type': 'multipart/form-data'}})
                    .then(() => {
                        Fire.$emit('afterCreate');
                        $('#addNew').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: 'Setting updated successfully'
                        });
                        document.getElementById('file').value = '';
                        this.$Progress.finish();
                    })
                    .catch(err => {
                        if(err.response.data.errors.slug)
                            err.response.data.errors['translations.ru.title'] = err.response.data.errors.slug;
                        this.$refs.validationObserver.setErrors(err.response.data.errors);
                        this.$Progress.fail();
                    });
            },
            getSettings(page = 1){
                axios.get(this.$http + 'settings?page=' + page).then(({data}) => (this.settings = data));
            },
            createPost() {
                this.$Progress.start();
                const data = new FormData();
                data.append('translations', JSON.stringify(this.form.translations));
                this.form.is_active === false ? data.append('is_active', 0) : '';
                if (this.file !== null) {
                    data.append('file', this.file);
                }
                axios.post(this.$http + 'settings', data, {headers: {'Content-Type': 'multipart/form-data'}})
                    .then(() => {
                        Fire.$emit('afterCreate');
                        $('#addNew').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: 'Setting created successfully'
                        });
                        document.getElementById('file').value = '';
                        this.$Progress.finish();
                    })
                    .catch(err => {
                        if(err.response.data.errors.slug)
                            err.response.data.errors['translations.ru.title'] = err.response.data.errors.slug;
                        this.$refs.validationObserver.setErrors(err.response.data.errors);
                        this.$Progress.fail();
                    });
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
                        axios.delete(this.$http + 'settings/' + id);
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
