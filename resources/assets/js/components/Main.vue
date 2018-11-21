<template>
  <div class="container-fluid">
    <div class="row">
      <Header v-bind:user="user"></Header> 
      <router-view></router-view>
    </div>
  </div>
</template>
<script>
import AuthApi from "../api/auth";
import Header from "./Header";

export default {
  components: {
    Header
  },

  data() {
    AuthApi.findMe()
      .then(res => {})
      .catch(err => {
        this.$router.push("/login");
      });
    return {
      user: JSON.parse(localStorage.getItem('auth')),
    };
  }
};
</script>
