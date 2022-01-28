import { createApp } from 'vue'
import App from './App.vue'
import axios from 'axios'
import router from "./router";

const app = createApp(App)
const that = app.config.globalProperties

that.$axios = axios

that.$isLoggedIn = false
that.$token = ''

that.$baseUrl = 'http://localhost:8080/';
that.$apiBaseUrl = 'http://127.0.0.1:8000'

that.$functions = {
    logout() {
        document.cookie = "token=; expires=; path=/;";
        window.location.href = that.$baseUrl
    },
    getRandomArbitrary(min, max) {
        return Math.round(Math.random() * (max - min) + min);
    },
    redirectHome() {
        if(window.location.href === that.$baseUrl) {
            window.location.reload(true)
        } else {
            window.location.href = that.$baseUrl
        }
    },
    setUser(user, result) {
        if(result !== null) {
            return user = result
        }
    },
    setCookie(cname, cvalue) {
        const d = new Date();
        d.setTime(d.getTime() + (2*60*60*1000));
        let expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    },
    getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i < ca.length; i++) {
          let c = ca[i];
          while (c.charAt(0) == ' ') {
            c = c.substring(1);
          }
          if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
          }
        }
        return "";
    },
    timeSince(date) {
        var seconds = Math.floor((new Date() - date) / 1000);
      
        var interval = seconds / 31536000;
      
        if (interval > 1) {
          return Math.floor(interval) + " years ago";
        }
        interval = seconds / 2592000;
        if (interval > 1) {
          return Math.floor(interval) + " months ago";
        }
        interval = seconds / 86400;
        if (interval > 1) {
          return Math.floor(interval) + " days ago";
        }
        interval = seconds / 3600;
        if (interval > 1) {
          return Math.floor(interval) + " hours ago";
        }
        interval = seconds / 60;
        if (interval > 1) {
          return Math.floor(interval) + " minutes ago";
        }
        return Math.floor(seconds) + " seconds ago";
    },
}

if(that.$functions.getCookie('token')) {
    that.$isLoggedIn = true
    that.$token = that.$functions.getCookie('token')
}

app.use(router)

app.mount('#app')
