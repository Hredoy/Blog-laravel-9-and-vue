<template>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h4 class="mb-5">Blog</h4>
                <table id="table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th class="check">
                                Action
                                <a
                                    data-toggle="modal"
                                    data-target="#post"
                                    style="
                                        float: right;
                                        cursor: pointer;
                                        color: white;
                                        padding: 2px;
                                    "
                                    @click="openModalWindow"
                                    class="btn btn-sm btn-warning py-0"
                                >
                                    <i class="fa fa-plus-square"> Add Post </i>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="post in posts.data" :key="post.id">
                            <td>{{ post.title }}</td>
                            <td>{{ post.description }}</td>
                            <td class="check">
                                <a
                                    title="Edit category"
                                    class="btn btn-sm btn-dark py-0"
                                    style="color: white; cursor: pointer"
                                    @click="edit(post)"
                                    >Edit</a
                                >
                                <a
                                    class="btn btn-sm btn-danger py-0"
                                    @click="deletePost(post.id)"
                                    style="color: white"
                                    >Delete</a
                                >
                            </td>
                        </tr>
                    </tbody>
                    <pagination
                        :data="posts"
                        @pagination-change-page="getResults"
                    ></pagination>
                </table>
                <div
                    class="modal fade"
                    id="post"
                    tabindex="-1"
                    role="dialog"
                    aria-labelledby="addNewLabel"
                    aria-hidden="true"
                >
                    <div
                        class="modal-dialog modal-dialog-centered"
                        role="document"
                    >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 v-if="form.id" class="modal-title">
                                    Update Post
                                </h5>
                                <h5 v-else class="modal-title">Add New Post</h5>
                            </div>

                            <form
                                @submit.prevent="
                                    form.id ? updatePost() : createPost()
                                "
                            >
                                <div class="modal-body">
                                    <div class="form-group">
                                        <input
                                            v-model="form.title"
                                            type="text"
                                            name="title"
                                            placeholder="title"
                                            class="input2 form-control"
                                            :class="{
                                                'is-invalid': errors.title,
                                            }"
                                            data-validate="title is required"
                                        />
                                        <span
                                            class="focus-input2"
                                            data-placeholder="title"
                                        ></span>
                                        <span
                                            class="text-danger"
                                            v-if="errors.title"
                                            >{{ errors.title[0] }}</span
                                        >
                                    </div>
                                    <div class="form-group">
                                        <input
                                            v-model="form.description"
                                            type="text"
                                            name="description"
                                            placeholder="description"
                                            class="input2 form-control"
                                            :class="{
                                                'is-invalid': errors.description,
                                            }"
                                            data-validate="description is required"
                                        />
                                        <span
                                            class="focus-input2"
                                            data-placeholder="description"
                                        ></span>
                                        <span
                                            class="text-danger"
                                            v-if="errors.description"
                                            >{{ errors.description[0] }}</span
                                        >
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button
                                        type="button"
                                        class="btn btn-danger"
                                        data-dismiss="modal"
                                    >
                                        Close
                                    </button>
                                    <button
                                        v-if="form.id"
                                        type="submit"
                                        class="btn btn-success"
                                    >
                                        Update
                                    </button>
                                    <button
                                        v-else
                                        type="submit"
                                        class="btn btn-success"
                                    >
                                        Create
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});

window.Toast = Toast;

let Fire = new Vue()
window.Fire = Fire;
export default {
    data() {
        return {
            posts: {},
            errors: [],
            form: {
                id: "",
                title: "",
                description: "",
            },
        };
    },
    methods: {
        resetForm() {
            for (var key in this.form) {
                this.form[key] = "";
            }
            this.errors = [];
        },
        openModalWindow() {
            this.resetForm();
            $("#post").modal("show");
        },
        getPosts() {
            axios.get("/api/posts").then((data) => (this.posts = data.data.posts));
        },
        getResults(page = 1) {
            axios.get("/api/posts?page=" + page).then((response) => {
                this.posts = response.data.posts;
            });
        },
        createPost() {
            axios
                .post("/api/posts", this.form)
                .then(() => {
                    Fire.$emit("load_post");

                    Toast.fire({
                        icon: "success",
                        title: "post created successfully",
                    });

                    $("#post").modal("hide");
                })
                .catch((error) => {
                    console.log("Error......");
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                });
        },
        edit(post) {
            this.resetForm();
            $("#post").modal("show");
            this.form = post;
        },
        updatePost() {
            axios
                .put("/api/posts/" + this.form.id, this.form)
                .then(() => {
                    Toast.fire({
                        icon: "success",
                        title: "post updated successfully",
                    });

                    Fire.$emit("load_post");

                    $("#post").modal("hide");
                })
                .catch((error) => {
                    console.log("Error.....");
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }
                });
        },
        deletePost(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.value) {
                    axios
                        .delete("/api/posts/" + id)
                        .then((response) => {
                            Swal.fire(
                                "Deleted!",
                                "post deleted successfully",
                                "success"
                            );

                            Fire.$emit("load_post");
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Something went wrong!",
                            });
                        });
                }
            });
        },
    },
    created() {
        this.getPosts();

        Fire.$on("load_post", () => {
            this.getPosts();
        });
    },
};
</script>

<style scoped>
.pagination {
    margin-top: 30px;
    float: right;
}

.validation_error {
    border: 1px solid red !important;
}
</style>
