<template>
    <div class="card form_login_card">
        <h2>SIGN UP</h2>
        <div v-if="sent" class="messages">
            <p 
            v-for="item in message" 
            :key="item" 
            :class="`response_message ${item === message.error ? 'error' : 'success'}`"
            > {{item}} </p>
        </div>
        <div class="form_login">
            <form :data-url="`${this.$apiBaseUrl}/api/register`" id="register" @submit.prevent="register">
                <div class="form_group">
                    <input type="text" name="username" class="form_control" placeholder="Your username..." v-model="form.username">
                </div>

                <div class="form_group">
                    <input type="password" name="password" class="form_control" placeholder="Your password..." v-model="form.password">
                </div>
                
                <div class="form_group">
                    <input type="password" name="confirm_password" class="form_control" placeholder="Confirm your password..." v-model="form.confirm_password">
                </div>
                
                <div class="form_group upload_form_group">
                    <input type="file" name="image" class="form_control custom-file-input">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#67727e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-upload"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                </div>
                
                <div class="form_group row">
                    <p><button class="btn btn-primary">Sign up</button></p>
                    <p>Already registered ? <router-link class="reply_btn" :to="'/login'">Log in.</router-link> </p>
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
                confirm_password: null,
            },
            message: {
                error: null,
                success: null
            },
            sent: false,
        }
    },
    methods: {
        register(event) {
            const url = event.target.dataset.url
            this.sent = true
            
            this.$axios.post(
                url,
                this.form,
                {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then((res) => {
                    this.sent = true
                    if(res.data.error) {
                        this.message.error = res.data.error
                    } else {
                        this.$router.push('/login')
                    }
                }).catch(() => {
                    this.message.error = "Something went wrong. Try again later."
                })
        }
    }
}
</script>