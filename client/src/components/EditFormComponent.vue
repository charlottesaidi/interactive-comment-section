<template>
    <form :id="formName" class="edit_comment_form" :action="editUrl" @submit.prevent="edit(container, replyingto)">
        <textarea name="content" v-model="form.content"></textarea>
        <button type="submit" class="btn btn-primary">update</button>
        <modal v-if="message.error">
            <p class="response_message error"> {{message.error}} </p>
            <div class="response_message">
                <button class="btn btn-primary btn-small" @click="this.$functions.redirectHome()">Back</button>
            </div>
        </modal>
    </form>
</template>

<script>
import Modal from './Modal.vue';
export default {
    props: {
        formName: String,
        editUrl: String,
        content: String,
        replyingto: String,
        container: Node
    },
    components: {
        Modal
    },
    data() {
        return {
            form: {
                content: this.content,
            },
            message: {
                error: null,
                success: null
            },
            sent: false,
        }
    },
    methods: {
        edit(container, replyingto) {
            this.$axios.post(
                this.editUrl, 
                this.form,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + this.$token
                    }
                })
                .then((res) => {
                    this.sent = true
                    if(res.data.error) {
                        this.message.error = res.data.error.message
                    } else {
                        if(replyingto) {
                            container.innerHTML = "<p class=\"comment_content\"><span class=\"replying_to\">"+replyingto+"</span> <span>"+this.form.content+"</span></p>"
                        } else {
                            container.innerHTML = `<p class="comment_content">${this.form.content}</span>`
                        }
                    }
                }).catch((err) => {
                    if(err.response.data.code === 401) {
                        this.$isLoggedIn = false
                        this.$router.push('/login')
                    } else {
                        this.message.error = err.response.data.detail
                    }
                })
        }
    }
}
</script>