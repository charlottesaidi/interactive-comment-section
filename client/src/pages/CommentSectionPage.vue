<template>
    <div class="comment_container">
        <div class="comment_loop" v-for="comment in comments" v-bind:key="comment.id">
            <comment-component class="comments"
                :object="comment"
                :contentId="`comment_content_${comment.id}`"
                :cardId="`comment_card_${comment.id}`"
                :content="comment.content" 
                :id="comment.id"
                :loginuser="this.$isLoggedIn ? getUser.username : null"
                :replyButtonAction="mountForm(comments)"
                :target="`for_comment_${comment.id}`"
                :deleteUrl="`${this.$apiBaseUrl}/api/delete-comment/${comment.id}`"
                :editUrl="`${this.$apiBaseUrl}/api/edit-comment/${comment.id}`"
            > 
                <div class="row score">
                    <IconPlus :target="`comment_score_${comment.id}`" @click="voteUp($event)"/>
                    <div class="comment_score" :id="`comment_score_${comment.id}`"> {{comment.score}} </div>
                    <IconMinus :target="`comment_score_${comment.id}`" @click="voteDown($event)"/>
                </div>
            </comment-component>

            <div class="reply_container" v-if="comment.replies.length">  
                <div v-for="reply in comment.replies" v-bind:key="reply.id" class="replies reply_loop">
                    <comment-component
                        :object="reply"
                        :scoreId="`reply_score_${reply.id}`"
                        :contentId="`reply_content_${reply.id}`"
                        :cardId="`reply_card_${reply.id}`"
                        :content="reply.content"
                        :id="comment.id"
                        :loginuser="this.$isLoggedIn ? getUser.username : null"
                        :replyButtonAction="mountForm(comment.replies)"
                        :target="`for_reply_${reply.id}`"
                        :deleteUrl="`${this.$apiBaseUrl}/api/delete-reply/${reply.id}`"
                        :editUrl="`${this.$apiBaseUrl}/api/edit-reply/${reply.id}`"
                    > 
                        <div class="row score">
                            <IconPlus :target="`reply_score_${reply.id}`" @click="voteUp($event)"/>
                            <div class="comment_score" :id="`reply_score_${reply.id}`"> {{reply.score}} </div>
                            <IconMinus :target="`reply_score_${reply.id}`" @click="voteDown($event)"/>
                        </div>
                    </comment-component>
                </div>
            </div> 

        </div>

        <CommentFormComponent v-if="this.$isLoggedIn"
            :image="getUser.image" 
            :button="'Send'" 
            :formName="`form_${this.$functions.getRandomArbitrary(0, 50)}`" 
            :url="`${this.$apiBaseUrl}/api/new-comment`"
            :placeholder="'Add a comment...'"
            :type="'comment'"
        />

    </div>
</template>

<script>
    import CommentComponent from '../components/CommentListComponent.vue';
    import CommentFormComponent from '../components/CommentFormComponent.vue';
    import IconPlus from '../components/svgs/IconPlusComponent.vue';
    import IconMinus from '../components/svgs/IconMinusComponent.vue';
    import { createApp, h } from 'vue'

    export default {
        components: {
            CommentComponent,
            CommentFormComponent,
            IconPlus,
            IconMinus,
        },
        data () {
            return {
                comments: [],
                user: [],
                message: null,
            }
        },
        created() {
            if(this.$isLoggedIn) {
                this.$axios.get(
                    `${this.$apiBaseUrl}/api/token`,
                    {
                        headers: {
                            'Authorization': 'Bearer ' + this.$token
                        }
                    }).then((res) => {
                        const exp = res.data[0].exp
                        const now = Math.floor(new Date() / 1000)
                        exp > now ? this.user = res.data[1] : document.cookie = "token=; expires=; path=/;"
                    }).catch((err) => {
                        this.message = err.response.data.detail
                    })
            }

            this.$axios.get(`${this.$apiBaseUrl}/api/comments`)
                .then(response => {
                    this.comments = response.data;
                }).catch((err) => {
                    this.message = err.response.data.detail
                })  
        },
        computed: {
            getUser() {
                return {
                    ...this.user, 
                    image: this.user.image && require(`../assets/images/avatars/${this.user.image}`)
                }
            }
        },
        methods: {
            mountForm(array) {
                var that = this;
                return function(event) {
                    const container = document.querySelector('#'+event.target.dataset.target)
                    const targetId = Number(event.target.dataset.target.substr(-1)) // reply to which respond
                    const feedId = this.id // main comment

                    const image = that.getUser.image
                    const getRandomInt = that.$functions.getRandomArbitrary(0, 20)
                    const apiUrl = that.$apiBaseUrl

                    const replyTo = array.find(el => el.id === targetId).user.username
                    
                    const app = createApp({ 
                        setup () {
                            return () => h(CommentFormComponent, 
                            {
                                image: image,
                                button: "Reply",
                                formName: `form_${getRandomInt}${feedId}`,
                                url: `${apiUrl}/api/new-reply`,
                                placeholder: `@${replyTo} `,
                                replyingto: replyTo,
                                type: 'reply'
                            })
                        }
                    })
                    app.config.globalProperties.$isLoggedIn = that.$isLoggedIn
                    app.config.globalProperties.$token = that.$token
                    app.config.globalProperties.$axios = that.$axios
                    app.config.globalProperties.$router = that.$router
                    app.config.globalProperties.$functions = that.$functions
                    app.config.globalProperties.$apiBaseUrl = apiUrl
                    app.mount(container)
                }
            },
            voteUp(event) {
                const target = document.querySelector(`#${event.target.dataset.target}`)
                target.innerText++
            },
            voteDown(event) {
                const target = document.querySelector(`#${event.target.dataset.target}`)
                target.innerText--
            },
        }
    }
</script>