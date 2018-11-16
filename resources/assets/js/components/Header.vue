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
                                    <a class="account" >{{ $t("header.client_dashboard") }}</a>
                                </p>
                            </li>
                        </ul>
                    </div>
                </li>
                </ul>
              </div>
        </div>
    </header>
</template>
<script>
export default {
    props: {
        user: {
            type: Object,
            default: null
        }
    },

    data () {
        return {
            currentUser: this.user,
            listUser: this.user.childCurrentUser
        };
    }
}
</script>