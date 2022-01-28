<template>
    <div class="row card reply_card relative">
        <div class="col">
            <span class="avatar">
                <img :src="image" class="logged_user_img" alt="Logged user avatar" width="40" height="40">
            </span> 
        </div>
        <div class="col form">
            <form :id="formName" class="comment_form" :data-class="type" :action="url" @submit.prevent="create">
                <textarea name="content" :placeholder="placeholder" v-model="form.content"></textarea>
                <button type="submit" class="btn btn-primary">{{button}}</button>
            </form>
        </div>
         
        <modal v-if="message.error">
            <p class="response_message error"
            > {{message.error}} </p>
            <div class="response_message">
                <button class="btn btn-primary btn-small" @click="this.$functions.redirectHome()">Back</button>
            </div>
        </modal>
    </div>
</template>

<script>
    import Modal from './Modal.vue';
    export default {
        props: {
            image: String,
            button: String,
            formName: String,
            url: String,
            placeholder: String,
            replyingto: String,
            type: String
        },
        components: {
            Modal
        },
        data() {
            return {
                form: {
                    content: null,
                    commentId: null,
                    replyingto: null                
                },
                message: {
                    error: null,
                    success: null
                },
            }
        },
        methods: {
            create(event) {
                const url = event.target.action;
                this.form.commentId = event.target.id.substr(-1)
                this.form.replyingto = this.replyingto

                this.$axios.post(
                    url, 
                    this.form,
                    {
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + this.$token
                        }
                    }).then((res) => {
                        if(res.data.error) {
                            this.message.error = res.data.error.message
                        } else { // Bugé !!
                            // this.message.success = res.data.success.message
                            this.$functions.redirectHome()
                        }
                    }).catch((err) => {
                        if(err.response) {
                            if(err.response.data.code === 401) {
                                this.$isLoggedIn = false
                                this.$router.push('/login')
                            } else {
                                console.log(err.response.data.detail)
                                this.message.error = err.response.data.detail
                            }
                        }
                    })
            },
        }
    }
</script>