<template>
  <div id="background" class="content">
    <p class="user-layout-title">安康电信指挥运营平台</p>
    <Row type="flex" justify="center" class-name="row">
      <br>
      <Col class="loginFrom">
        <Tabs value="name1">
          <TabPane label="账户密码登录" name="name1"></TabPane>
        </Tabs>
        <Form ref="form" :model="form" :rules="ruleInline">
          <FormItem prop="email">
            <Input type="text" size="large" v-model="form.email" :placeholder="$t('login.username')" placeholder="">
              <Icon type="md-person" slot="prepend"></Icon>
            </Input>
          </FormItem>
          <FormItem prop="password" class="bottom">
            <Input type="password" size="large" v-model="form.password" :placeholder="$t('login.password')" placeholder="">
              <Icon type="md-lock" slot="prepend"></Icon>
            </Input>
          </FormItem>
          <FormItem prop="verifyCode">
            <Input type="text" size="large" v-model="form.verifyCode" placeholder="请输入验证码（不区分大小写）"
                   style="width: 238px;"/>
            <vue-img-verify @getImgCode="getImgCode" ref="vueImgVerify"/>
          </FormItem>
          <FormItem class="bottom">
            <Checkbox size="large" v-model="form.remember" class="rememberMe">记住我</Checkbox>
            <router-link to="/password/send" class="forgetPassword">
              <span>忘记密码</span>
            </router-link>
          </FormItem>
          <FormItem class="bottom">
            <Button type="primary" @click="submit('form')" id="loginButton" size="large" long :loading="loading">
              登录
            </Button>
          </FormItem>
        </Form>
      </Col>
    </Row>
  </div>
</template>

<script>
  import './login.css'
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
        verifyCodeCheckValidate,
        imgCode: '',
        loading: false,
        form: {
          email: '',
          password: '',
          verifyCode: '',
          remember: false
        },
        // vaptchaObj: {},
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
              // this.vaptchaObj.reset(); //重置验证码
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
