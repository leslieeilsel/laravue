<template>
  <div id="background" class="content">
    <p class="user-layout-title">陕西体育彩票财务数据综合管理系统</p>
    <Row type="flex" justify="center" class-name="row">
      <br>
      <Col class="loginFrom">
        <Tabs value="name1">
          <TabPane label="账户密码登录" name="name1"></TabPane>
        </Tabs>
        <Form ref="form" :model="form" :rules="ruleInline">
          <FormItem prop="email">
            <Input type="text" size="large" v-model="form.email" :placeholder="$t('login.email')">
            <Icon type="md-mail" slot="prepend"></Icon>
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
          <FormItem class="bottom">
            <Button type="primary" @click="submit('form')" size="large" long :loading="loading">登录</Button>
          </FormItem>
        </Form>
      </Col>
    </Row>
  </div>
</template>

<script>
  import './login.css' /*引入公共样式*/
  export default {
    name: "index",
    data () {
      return {
        loading: false,
        form: {
          email: '',
          password: '',
          remember: false
        },
        ruleInline: {
          email: [
            { required: true, message: '请填写邮箱地址', trigger: 'blur' },
            { type: 'email', message: '请填写正确的邮箱地址', trigger: 'blur' }
          ],
          password: [
            { required: true, message: '请填写登录密码', trigger: 'blur' },
            { type: 'string', min: 6, message: '密码长度不能低于6个字符', trigger: 'blur' }
          ]
        }
      }
    },
    methods: {
      submit (name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            this.$store.dispatch('login', this.form).then(() => {
              this.loading = false;
              this.$router.push('/');
            })
          }
        })
      }
    },
    mounted () {
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
