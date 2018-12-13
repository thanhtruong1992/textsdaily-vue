<template>
  <div class="container-fluid">
    <div class="row">
      <Header></Header> 
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
    return {};
  }
};
</script>
