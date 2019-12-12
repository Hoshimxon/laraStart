<style>
    .widget-user-header {
        background-position: center;
        background-size: cover;
        height: 300px!important;
    }
</style>

<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-5">
                <div class="card card-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header text-white" style="background-image: url('./img/cybertruckjpg.jpg')">
                        <h3 class="widget-user-username text-right">{{form.name}}</h3>
                        <h5 class="widget-user-desc text-right">{{form.type}}</h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" :src="getProfilePhoto()" alt="User Avatar">
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">3,200</h5>
                                    <span class="description-text">SALES</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">13,000</h5>
                                    <span class="description-text">FOLLOWERS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4">
                                <div class="description-block">
                                    <h5 class="description-header">35</h5>
                                    <span class="description-text">PRODUCTS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Activity</a></li>
                            <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane" id="activity">
                            </div>
                            <!-- /.tab-pane -->

                            <div class="tab-pane active" id="settings">
                                <form class="form-horizontal">
                                    <div class="form-group">
                                        <label for="inputName" class="form-label">Name</label>
                                        <input type="text" v-model="form.name" class="form-control" :class="{ 'is-invalid': form.errors.has('name')}" id="inputName" placeholder="Name">
                                        <has-error :form="form" field="name"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail" class="form-label">Email</label>
                                        <input type="email" v-model="form.email" class="form-control" id="inputEmail" placeholder="Email" :class="{'is-invalid': form.errors.has('email')}">
                                        <has-error :form="form" field="email"></has-error>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputExperience" class="form-label">Experience</label>
                                        <textarea v-model="form.bio" class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputFile">Profile Photo</label>
                                        <div class="input-group">
                                            <input type="file" accept="image/*" name="photo" @change="updatePhoto" id="exampleInputFile">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputPassport" class="form-label">Password(leave empty if not changing)</label>
                                        <input v-model="form.password" type="password" class="form-control" id="inputPassport" placeholder="Password" :class="{'is-invalid': form.errors.has('password')}">
                                        <has-error :form="form" field="password"></has-error>
                                    </div>

                                    <div class="form-group">
                                        <button @click.prevent="updateProfile" type="submit" class="btn btn-success">Update</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: '',
                })
            }
        },
        created() {
            axios.get("api/profile")
                .then(({data}) => (this.form.fill(data)));
        },
        methods: {
            getProfilePhoto() {
                let photo = (this.form.photo.length > 200) ? this.form.photo : "img/profile/" + this.form.photo;
                return photo;
            },
            updatePhoto(e) {
                let file = e.target.files[0];
                let reader = new FileReader();
                if (file['size'] < 2111775) {
                    reader.onloadend = (file) => {
                        this.form.photo = reader.result;
                    };
                    reader.readAsDataURL(file);
                }else {
                    this.form.photo = '';
                    document.getElementById('exampleInputFile').value = '';
                    Swal.fire(
                        'Oops',
                        'You are uploading a large file',
                        'warning'
                    )
                }
            },
            updateProfile() {
                this.$Progress.start();
                this.form.put('api/profile')
                    .then(() => {
                        Swal.fire(
                            'Updated',
                            'User updated successfully',
                            'success'
                        );
                        document.getElementById('exampleInputFile').value = '';
                        this.$Progress.finish();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    })
            }
        },
    }
</script>
