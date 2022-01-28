<template>
    <div class="container">
        <header>
            <nav class="relative">
                <ul class="nav">
                    <li>
                        <router-link class="home_icon" :to="'/'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5357B6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        </router-link>
                    </li>
                    <li v-if="!this.$isLoggedIn">
                        <router-link class="login_icon" :to="'/login'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5357B6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </router-link>
                    </li>
                    <li v-else>
                        <a class="logout_icon" @click.prevent="this.$functions.logout">
                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sign-out-alt" class="feather svg-inline--fa fa-sign-out-alt fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="24" height="24"><path fill="#5357B6" d="M497 273L329 441c-15 15-41 4.5-41-17v-96H152c-13.3 0-24-10.7-24-24v-96c0-13.3 10.7-24 24-24h136V88c0-21.4 25.9-32 41-17l168 168c9.3 9.4 9.3 24.6 0 34zM192 436v-40c0-6.6-5.4-12-12-12H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h84c6.6 0 12-5.4 12-12V76c0-6.6-5.4-12-12-12H96c-53 0-96 43-96 96v192c0 53 43 96 96 96h84c6.6 0 12-5.4 12-12z"></path></svg>
                        </a>
                    </li>
                    <li>
                        <div class="wrapper">
                            <input type="checkbox" name="checkbox" class="switch" id="button">

                            <svg v-if="light" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5357B6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>

                            <svg v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5357B6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                            
                        </div>
                    </li>
                </ul>
            </nav>
        </header>
        <router-view/>
    </div>
</template>

<script>
export default {
    name: 'App',
    data() {
        return {
            light: true
        }
    },
    mounted() {
        const toggleSwitch = document.querySelector(".switch")
        const currentTheme = localStorage.getItem("theme");
        
        if (currentTheme) {
            document.body.setAttribute("theme", currentTheme);
            if (currentTheme === "dark") {
                toggleSwitch.checked = true;
                this.light = false;
            }
        }

        toggleSwitch.addEventListener("change", this.switchTheme, false);
    },
    methods: {
        switchTheme(event) {
            if (event.target.checked) {
                document.body.setAttribute("theme", "dark");
                localStorage.setItem("theme", "dark");
                this.light = false;
            } else {
                document.body.setAttribute("theme", "light");
                localStorage.setItem("theme", "light");
                this.light = true;
            }
        }
    }
}
</script>

<style>
    @import './assets/stylesheets/app.css';
</style>
