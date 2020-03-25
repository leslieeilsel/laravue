<template>
  <div class="content" id="background">
    <Row class="header">
      <!-- <img alt="" src="../../images/logo.png" style="width:320px"/> -->
      <p class="description">{{title}}</p>
    </Row>
    <Row @keydown.enter.native="submit('form')" class-name="row" justify="center" type="flex">
      <br>
      <Col class="loginFrom">
        <Form :model="form" :rules="ruleInline" ref="form">
          <FormItem prop="email">
            <Input :placeholder="$t('login.username')" size="large" type="text" v-model="form.email">
              <Icon slot="prepend" type="md-person"></Icon>
            </Input>
          </FormItem>
          <FormItem prop="password">
            <Input :placeholder="$t('login.password')" size="large" type="password" v-model="form.password">
              <Icon slot="prepend" type="md-lock"></Icon>
            </Input>
          </FormItem>
          <FormItem prop="verifyCode">
            <Input placeholder="请输入验证码（不区分大小写）" size="large" style="width: 238px;" type="text"
                   v-model="form.verifyCode"/>
            <vue-img-verify @getImgCode="getImgCode" ref="vueImgVerify"/>
          </FormItem>
          <FormItem class="bottom">
            <Checkbox class="rememberMe" size="large" v-model="form.remember">自动登录</Checkbox>
            <router-link class="forgetPassword" to="/password/send">
              <span>忘记密码</span>
            </router-link>
          </FormItem>
          <FormItem class="bottom">
            <Button :loading="loading" @click="submit('form')" id="loginButton" long size="large" type="primary">
              登录
            </Button>
          </FormItem>
        </Form>
      </Col>
    </Row>
  </div>
</template>

<script>
  import './login.css';
  import vueImgVerify from '../../components/ImgVerify';

  const extend = function (to, _from) {
    for (const key in _from) {
      to[key] = _from[key]
    }
    return to
  };
  export default {
    name: "index",
    components: {
      vueImgVerify
    },
    data() {
      const verifyCodeCheckValidate = (rule, value, callback) => {
        let verifyCode = this.form.verifyCode.toLowerCase();
        let imgCode = this.imgCode.toLowerCase();
        if (verifyCode !== '') {
          if (verifyCode !== imgCode) {
            callback(new Error('验证码错误，请重新输入'));
          } else {
            callback();
          }
        } else {
          callback(new Error('请输入验证码'));
        }
      };
      return {
        title: this.$store.state.settings.title,
        verifyCodeCheckValidate,
        imgCode: '',
        loading: false,
        form: {
          email: '',
          password: '',
          verifyCode: '',
          remember: false
        },
        ruleInline: {
          email: [
            {required: true, message: '请填写用户名', trigger: 'blur'},
          ],
          password: [
            {required: true, message: '请填写登录密码', trigger: 'blur'},
          ],
          verifyCode: [
            {required: true, validator: verifyCodeCheckValidate, trigger: 'blur'}
          ]
        }
      }
    },
    methods: {
      submit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            this.$store.dispatch('login', this.form).then((res) => {
              this.loading = false;
              this.$router.push({name: 'home'})
            });
          }
        })
      },
      // 点击图片获取验证码
      getImgCode(code) {
        this.imgCode = code;
        this.form.verifyCode = '';
        // console.log('验证码: ' + this.imgCode);
      },
    },
    mounted() {
      this.imgCode = this.$refs.vueImgVerify.draw();
    }
  }
</script>

<style lang="scss" rel="stylesheet/scss" scoped>
  .loginFrom {
    width: 368px;
  }

  .row {
    padding-top: 100px;

    .bottom {
      margin-bottom: 14px;

      .rememberMe {
        margin-left: 5px;
      }

      .forgetPassword {
        float: right;
      }
    }
  }
</style>
