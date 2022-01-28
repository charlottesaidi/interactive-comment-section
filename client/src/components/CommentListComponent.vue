<template>
    <div :id="cardId+id">
        <div class="card row">
            <slot/>
            <div class="col comment">
                <div class="author row">
                    <div class="col">
                    <p class="author">
                        <span class="avatar"><img :src="require(`../assets/images/avatars/${object.user.image}`)" alt="Comment's user avatar" width="32" height="32"></span>
                        <span class="username"> {{object.user.username}}  </span>
                        <span v-if="loginuser === object.user.username" class="badge"> you </span>
                        <span class="date"> {{ this.$functions.timeSince(new Date(object.createdAt.timestamp * 1000)) }} </span> 
                    </p>
                    </div>
                    <div class="col">
                        <div class="buttons row" v-if="loginuser === object.user.username">
                            <button class="btn delete_button" @click="openModal">
                                <span class="delete_icon"><img src="../assets/images/icon-delete.svg" alt="Icon delete"></span> Delete
                            </button>
                            <button class="btn reply_button" :data-target="contentId+id" @click="mountEditform">
                                <span class="edit_icon"><Edit/></span> Edit
                            </button>
                        </div>
                        <button v-if="this.$isLoggedIn && loginuser !== object.user.username" class="btn reply_button reply" @click="replyButtonAction" :data-target="target">
                            <span class="reply_icon"><Reply/></span> Reply
                        </button>
                    </div>
                </div>
                <div class="row" :id="contentId+id">
                    <p class="comment_content"> <span class="replying_to" v-if="object.replyingTo"> @{{object.replyingTo}} </span> <span> {{content}} </span></p>
                </div>
            </div>
        </div>

        <div :id="target" :data-ref="id"></div>
         
        <modal v-if="request">
            <h2>Delete comment</h2>
            <p> Are you sure you want to delete this comment? This will remove the comment and can’t be undone. </p>
            <div class="response_message delete_modal">
                <button class="btn btn-secondary btn-large" @click="request = false">No, cancel</button>
                <button class="btn btn-danger btn-large" :data-url="deleteUrl" @click="deleteComment($event)">Yes, delete</button>
            </div>
        </modal>
        <modal v-if="message.error">
            <p class="response_message error"
            > {{message.error}} </p>
            <div class="response_message">
                <button class="btn btn-primary btn-small" @click="message.error = null">Back</button>
            </div>
        </modal>
    </div>
</template>

<script>
    import Modal from './Modal.vue';
    import EditFormComponent from './EditFormComponent.vue';
    import { createApp, h } from 'vue'
    import Edit from '../components/svgs/EditIconComponent.vue';
    import Reply from '../components/svgs/ReplyIconComponent.vue';

    export default {
        props: {
            object: Object,
            contentId: String,
            cardId: String,
            content: String,
            id: Number,
            loginuser: String,
            target: String,
            replyButtonAction: Function,
            deleteUrl: String,
            editUrl: String,
        },
        components: {
            Modal,
            Reply,
            Edit,
        },
        data() {
            return {
                request: false,
                confirmed: false,
                message: {
                    error: null,
                    success: null
                }
            }
        },
        methods: {
            openModal() {
                this.request = true
                console.log(this)
            },
            deleteComment(event) {
                const url = event.target.dataset.url
                const node = document.querySelector('#'+this.cardId+this.id)
                
                this.$axios.delete(
                    url,
                    {
                        headers: {
                            'Authorization': 'Bearer ' + this.$token
                            }
                    })
                    .then((res) => {
                        if(res.data.error) {
                            this.message.error = res.data.error.message
                        } else {
                            node.parentElement.removeChild(node);
                            this.request = false
                        }
                    }).catch((err) => {
                        if(err.response.data.code === 401) {
                            this.$isLoggedIn = false
                            this.$router.push('/login')
                        }
                    })
            },
            mountEditform(event) {
                const container = document.querySelector('#'+event.target.dataset.target)
                const url = this.editUrl 
                const getRandomInt = this.$functions.getRandomArbitrary(0, 50)
                const content = this.content
                let replyingto = null
                if(document.querySelector(`#${event.target.dataset.target} .replying_to`)) {
                    replyingto = document.querySelector(`#${event.target.dataset.target} .replying_to`).innerText
                }

                const app = createApp({ 
                    setup () {
                        return () => h(EditFormComponent, 
                        {
                            formName: `edit_form_${getRandomInt}`, 
                            editUrl: url,
                            content: content,
                            container: container,
                            replyingto: replyingto
                        })
                    }
                })

                container.innerHTML = '';
                
                app.config.globalProperties.$isLoggedIn = this.$isLoggedIn
                app.config.globalProperties.$token = this.$token
                app.config.globalProperties.$axios = this.$axios
                app.config.globalProperties.$router = this.$router
                app.config.globalProperties.$functions = this.$functions
                app.config.globalProperties.$apiBaseUrl = this.$$apiBaseUrl
                app.mount(container)
            }
        }
    }
</script>