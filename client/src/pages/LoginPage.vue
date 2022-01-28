<template>
    <div class="card form_login_card">
        <h2>LOG IN</h2>
        <div v-if="sent" class="messages">
            <p 
            v-for="item in message" 
            :key="item" 
            :class="`response_message ${item === message.error ? 'error' : 'success'}`"
            > {{item}} </p>
        </div>
        <div class="form_login">
            <form :data-url="`${this.$apiBaseUrl}/api/login_check`" id="login" @submit.prevent="login">
                <div class="form_group">
                    <input type="text" name="username" class="form_control" placeholder="Your username..." v-model="form.username">
                </div>

                <div class="form_group">
                    <input type="password" name="password" class="form_control" placeholder="Your password..." v-model="form.password">
                </div>
                
                <div class="form_group row">
                    <p><button class="btn btn-primary">Login</button></p>
                    <p>Not registered ? <router-link class="reply_btn" :to="'/register'">Create an account.</router-link> </p>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                username: null,
                password: null,
            },
            message: {
                error: null,
                success: null
            },
            sent: false,
        }
    },
    methods: {
        login(event) {
            const url = event.target.dataset.url
            this.sent = true

            this.$axios.post(
                url,
                this.form,
                {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then((res) => {
                    this.$functions.setCookie('token', res.data.token)
                    this.$functions.redirectHome()
                }).catch((err) => {
                    this.message.error = err.response.data.message
                })
        }
    }

}
</script>