<template>
  <div id="background" class="content">
    <p class="user-layout-title">沣西云投资平台</p>
    <Row type="flex" justify="center" class-name="row">
      <br>
      <Col class="loginFrom">
        <Tabs value="name1">
          <TabPane label="账户密码登录" name="name1"></TabPane>
        </Tabs>
        <Form ref="form" :model="form" :rules="ruleInline">
          <FormItem prop="email">
            <Input type="text" size="large" v-model="form.email" :placeholder="$t('login.username')">
              <Icon type="md-person" slot="prepend"></Icon>
            </Input>
          </FormItem>
          <FormItem prop="password" class="bottom">
            <Input type="password" size="large" v-model="form.password" :placeholder="$t('login.password')">
              <Icon type="md-lock" slot="prepend"></Icon>
            </Input>
          </FormItem>
          <FormItem class="bottom">
            <Checkbox size="large" v-model="form.remember" class="rememberMe">记住我</Checkbox>
            <router-link to="/password/send" class="forgetPassword">
              <span>忘记密码</span>
            </router-link>
          </FormItem>
          <div ref="vaptcha" class="bottom" style="width: 368px;height: 40px">
            <div class="vaptcha-init-main">
              <div class="vaptcha-init-loading">
                <a href="https://www.vaptcha.com/" target="_blank">
                  <img src="https://cdn.vaptcha.com/vaptcha-loading.gif" alt=""/>
                </a>
                <span class="vaptcha-text">Vaptcha启动中...</span>
              </div>
            </div>
          </div>
          <FormItem class="bottom">
            <Button type="primary" @click="submit('form')" :disabled="disabled" id="loginButton"
                    size="large" long :loading="loading">登录
            </Button>
          </FormItem>
        </Form>
      </Col>
    </Row>
  </div>
</template>

<script>
  import './login.css'

  const extend = function (to, _from) {
    for (const key in _from) {
      to[key] = _from[key]
    }
    return to
  };
  export default {
    props: {
      type: {
        type: String,
        default: 'click'
      },
      scene: {
        type: String,
        default: ''
      },
      vpStyle: {
        type: String,
        default: 'light'
      },
      color: {
        type: String,
        color: '#2d8cf0'
      },
      lang: {
        type: String,
        default: 'zh-CN'
      },
    },
    name: "index",
    data() {
      return {
        disabled: true,
        loading: false,
        form: {
          email: '',
          password: '',
          remember: false
        },
        vaptchaObj: {},
        ruleInline: {
          email: [
            {required: true, message: '请填写用户名', trigger: 'blur'},
          ],
          password: [
            {required: true, message: '请填写登录密码', trigger: 'blur'},
          ]
        }
      }
    },
    methods: {
      loadV2Script() {
        if (typeof window.vaptcha === 'function') { //如果已经加载就直接放回
          return Promise.resolve()
        } else {
          return new Promise(resolve => {
            let script = document.createElement('script');
            script.src = 'https://cdn.vaptcha.com/v2.js';
            script.async = true;
            script.onload = script.onreadystatechange = function () {
              if (!this.readyState || this.readyState === 'loaded' || this.readyState === 'complete') {
                resolve();
                script.onload = script.onreadystatechange = null
              }
            };
            document.getElementsByTagName("head")[0].appendChild(script)
          })
        }
      },
      submit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            this.$store.dispatch('login', this.form).then((res) => {
              this.vaptchaObj.reset(); //重置验证码
              this.loading = false;
              this.$router.push({name: 'home'})
            });
          }
        })
      }
    },
    mounted() {
      let config = extend({
        vid: '5bffa64bfc650eb8507698ed',
        container: this.$refs.vaptcha,
        style: this.vpStyle
      }, this.$props);
      this.loadV2Script().then(() => {
        window.vaptcha(config).then(obj => {
          this.$emit('input', obj);
          let _this = this;
          _this.vaptchaObj = obj;
          obj.listen('pass', function () {
            _this.disabled = false;
          });
          obj.render()
        })
      })
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
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
