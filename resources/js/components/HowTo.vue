<template>
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">How to</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" @click="newModal">Adda New <i class="fa fa-user-plus"></i></button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Position</th>
                                <th>Title {{this.$language}}</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="post in posts.data" :key="post.id">
                                <td>{{post.position}}</td>
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
                        <pagination :data="posts" @pagination-change-page="getPosts"></pagination>
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
                                            <label>Content {{translation.locale}}</label>
                                            <textarea v-model="translation.content" class="form-control"></textarea>
                                            <!--                                        <vue-editor v-model="translation.content" />-->
                                        </div>
                                    </template>
                                    <div v-if="modalMode === 'edit'" class="form-group">
                                        <label>Position</label>
                                        <validation-provider rules="numeric" v-slot="{ errors }" vid="position">
                                            <input v-model="form.position" name="position" class="form-control">
                                            <span class="validation-info">{{ errors[0] }}</span>
                                        </validation-provider>
                                    </div>
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
                                            <input type="file" id="file" accept="image/*" @change="selectFile" multiple>
                                        </div>
                                    </div>
                                    <div v-if="form.files.length" class="form-group">
                                        <label>Current image</label>
                                        <div class="input-group post-images-group">
                                            <div v-for="file in form.files" class="post-images">
                                                <a href="#" @click="deletePostImage(file.id)">
                                                    <i class="fa fa-trash-alt red"></i>
                                                </a>
                                                <img class="img-circle" width="100px" :src="file.file_url" alt="Post image">
                                            </div>
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
                posts: {},
                modalMode: 'create',
                form: {
                    position: 0,
                    translations: [],
                    is_active: true,
                    internal_link: '',
                    external_link: '',
                    files: [],
                },
                file: null,
                fileIds: []
            }
        },
        created() {
            this.getPosts();
            Fire.$on('afterCreate', () => {
                this.getPosts();
            });
            this.setTranslations();
        },
        methods: {
            deletePostImage(id) {
                this.form.files = this.form.files.filter(file => {
                    return file.id !== id;
                });
                this.fileIds.push(id);
            },
            selectFile(e) {
                this.file = e.target.files;
            },
            setTranslations() {
                this.$translations.forEach(item => {
                    this.form.translations.push({
                        title: '',
                        content: '',
                        locale: item
                    });
                });
            },
            newModal() {
                this.modalMode = 'create';
                this.form.translations = [];
                this.form.files = [];
                this.setTranslations();
                this.form.is_active = true;
                $('#addNew').modal('show');
            },
            editPost(post) {
                this.form = post;
                this.fileIds = [];
                this.modalMode = 'edit';
                $('#addNew').modal('show');
            },
            updatePost() {
                this.$Progress.start();
                const data = new FormData();
                data.append('translations', JSON.stringify(this.form.translations));
                this.form.is_active === false ? data.append('is_active', 0) : '';
                data.append('position', this.form.position);

                if(this.file !== null) {
                    for(let i = 0; i < this.file.length; i++) {
                        data.append('file[' + i + ']', this.file[i]);
                    }
                }

                data.append('post_id', JSON.stringify(this.fileIds));

                data.append('_method', 'put');
                axios.post(this.$http + 'posts/' + this.form.id, data, {headers: {'Content-Type': 'multipart/form-data'}})
                    .then(() => {
                        Fire.$emit('afterCreate');
                        $('#addNew').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: 'Post updated successfully'
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
            getPosts(page = 1){
                axios.get(this.$http + 'posts/how-to?page=' + page).then(({data}) => (this.posts = data));
            },
            createPost() {
                this.$Progress.start();
                const data = new FormData();
                data.append('translations', JSON.stringify(this.form.translations));
                this.form.is_active === false ? data.append('is_active', 0) : '';
                if (this.file !== null) {
                    for(let i = 0; i < this.file.length; i++) {
                        data.append('file[' + i + ']', this.file[i]);
                    }
                }
                axios.post(this.$http + 'posts/how-to', data, {headers: {'Content-Type': 'multipart/form-data'}})
                    .then(() => {
                        Fire.$emit('afterCreate');
                        $('#addNew').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: 'Post created successfully'
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
                        axios.delete(this.$http + 'posts/' + id);
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
