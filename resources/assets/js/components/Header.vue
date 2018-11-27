<template>
    <header class="header">
        <div class="top">
            <div class="logo col">
                <img class="logo-dashboard" src="images/textdaily.png" />
            </div>
            <div class="col">
                <ul class="menu-header">
                    <li>
                        <a href="#">Hey, {{ currentUser.name }}!</a>
                    </li>
                    <li v-if="listUser.length > 0">
                        <a class="font-bold" href="#">{{ $t("header.select_client") }}</a>
                        <div class="sub-menu border pb-3 bt-3">
                            <ul>
                                <li v-for="(item, key) in listUser">
                                    <p class="title">{{ item.name }}</p>
                                    <p class="action-menu">
                                        <span v-if="item.status == 'DISABLED'" class="badge badge-secondary">DISABLED</span>
                                        <a class="edit" >{{ $t("header.edit") }}</a>
                                        <a class="account" v-on:click="switchAccount(item.id)">{{ $t("header.client_dashboard") }}</a>
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li v-if="currentUser.other_role && currentUser.other_role.length > 0">
                        <a v-on:click="returnParent()">{{ $t("header.return_original_user") + currentUser.other_role[0] }}</a>
                    </li>
                    <li v-if="currentUser.type == 'GROUP1' || currentUser.type == 'GROUP2'">
                        <a href="#">{{ $t("header.menu.setting") }}</a>
                    </li>
                    <li>
                        <a v-on:click="logOut()">{{ $t("header.logout") }}</a>
                    </li>
                    <li v-if="currentUser.type != 'GROUP4'">
                        <a href="#" target="_blank">{{ $t("header.help") }}</a>
                    </li>
                </ul>
              </div>
        </div>
        <div class="bottom">
            <ul class="menu">
                <div v-if="currentUser.type == 'GROUP4'">
                    <router-link tag="li" to="/dashboard" class="dashboard">
                        <a>{{ $t("header.menu.dashboard") }}</a>
                    </router-link>
                    <li class="subscribers">
                        <a href="#">{{ $t("header.menu.subscriber") }}</a>
                    </li>
                    <li class="campaigns">
                        <a href="#">{{ $t("header.menu.campaign") }}</a>
                    </li>
                    <li class="report">
                        <a href="#">{{ $t("header.menu.report") }}</a>
                    </li>
                </div>
                <div v-if="currentUser.type == 'GROUP3'">
                    <router-link tag="li" to="/dashboard" class="dashboard">
                        <a>{{ $t("header.menu.dashboard") }}</a>
                    </router-link>
                    <li class="report">
                        <a href="#">{{ $t("header.menu.report") }}</a>
                    </li>
                    <li class="token">
                        <a href="#">{{ $t("header.menu.token") }}</a>
                    </li>
                </div>
                <div v-if="currentUser.type == 'GROUP2'">
                    <router-link tag="li" to="/dashboard" class="dashboard">
                        <a>{{ $t("header.menu.dashboard") }}</a>
                    </router-link>
                    <li class="client">
                        <a href="#">{{ $t("header.menu.my_client") }}</a>
                    </li>
                    <li class="transaction header-transaction">
                        <a href="#">{{ $t("header.menu.transaction_history") }}</a>
                    </li>
                    <li class="report">
                        <a href="#">{{ $t("header.menu.report") }}</a>
                    </li>
                </div>
                <div v-if="currentUser.type == 'GROUP1'">
                    <router-link tag="li" to="/dashboard" class="dashboard">
                        <a>{{ $t("header.menu.dashboard") }}</a>
                    </router-link>
                    <router-link tag="li" to="/clients" class="client">
                        <a>{{ $t("header.menu.my_client") }}</a>
                    </router-link>
                    <li class="transaction header-transaction">
                        <a href="#">{{ $t("header.menu.transaction_history") }}</a>
                    </li>
                    <li class="report">
                        <a href="#">{{ $t("header.menu.report") }}</a>
                    </li>
                </div>
            </ul>
        </div>
    </header>
</template>
<script>
import AuthApi from "../api/auth";

export default {
  props: {
    user: {
      type: Object,
      default: null
    }
  },

  data() {
    return {
      currentUser: this.user,
      listUser: this.user.childCurrentUser
    };
  },

  methods: {
    switchAccount(id) {
      AuthApi.switchAccount(id)
        .then(res => {
          this.currentUser = res;
          this.listUser = res.childCurrentUser;
        })
        .catch(err => {});
    },

    returnParent() {
      AuthApi.returnParent()
        .then(res => {
          this.currentUser = res;
          this.listUser = res.childCurrentUser;
        })
        .catch(err => {});
    },

    logOut () {
        AuthApi.logOut()
            .then(res => {
                this.$router.push("/login");
            })
            .catch(err => {

            });
    }
  }
};
</script>