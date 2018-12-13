<template>
    <div class="container-fluid bg-login">
        <div class="row height-full">
            <div class="register">
                <div class="content">
                    <div>
                        <img class="logo" alt="" src="images/textdaily.png"  />
                    </div>
                    <form action="#" @submit="onSubmit" id="registerForm" class="form-register">
                        <h5 class="title-login">{{ $t("login.title") }}</h5>
                        <div class="form-group">
                            <label for="exampleInputEmail1">{{ $t('login.username') }}</label>
                            <input
                                type="text"
                                name="username"
                                v-model="username"
                                data-rule-required="true"
                                class="form-control form-control-sm"
                                :placeholder="$t('login.username')"
                                :data-msg-required="$t('validationForm.username.required')"
                            />
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">{{ $t('login.password') }}</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                v-model="password"
                                data-rule-required="true"
                                class="form-control form-control-sm"
                                :placeholder="$t('login.placeholder.password')"
                                :data-msg-required="$t('validationForm.password.required')"
                            />
                        </div>
                        <div class="form-group">
                            <a class="forgot-password" href="/forgot-password">I forgot my password?</a>
                        </div>
                        <button type="submit" id="btn-register" class="btn btn-register">{{ $t('login.login') }}</button>
                        <div>
                            <span>{{ $t('login.best_view')}}:</span>
                            <span>
                                <img src="images/IE.png" />
                                IE 11
                            </span>
                            <span>
                                <img src="images/firefox.png" />
                                Firefox 57
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
require('jquery-validation');
import AuthApi from "../../api/auth";
import Notification from "../../notify/index";

export default {
  data() {
    // check logined
    if(localStorage.getItem('auth')) {
        this.$router.push('/');
    }
    return {
      username: "",
      password: ""
    };
  },
  methods: {
    onSubmit(e) {
      e.preventDefault();
      if (!!$("#registerForm").valid()) {
        AuthApi.login({
          username: this.username,
          password: this.password
        }).then(res => {
            // redirect to dashboard
            this.$user = res;
            this.$router.push("/dashboard");
        }).catch(err => {
            this.$user = null;
            if(err.message) {
                Notification.show('error', {
                    content: err.message
                });
            }
        });
      }
    }
  },
  mounted() {
    $("#registerForm").validate();
  }
};
</script>
