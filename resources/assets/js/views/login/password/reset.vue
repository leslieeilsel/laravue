<template>
  <div>
    <div id="particles-js"></div>
    <Row class-name="row" justify="center" type="flex">
      <Col span="6">
        <Card>
          <p slot="title">重置密码</p>
          <Alert closable show-icon type="success" v-show="showAlert">
            密码已经被重置！
          </Alert>
          <Form :model="form" :rules="rules" ref="form">
            <FormItem class="bottom" label="邮箱" prop="email">
              <Input disabled v-model="form.email"></Input>
            </FormItem>
            <FormItem class="bottom" label="新密码" prop="password">
              <Input type="password" v-model="form.password"></Input>
            </FormItem>
            <FormItem label="确认密码" prop="password_confirmation">
              <Input type="password" v-model="form.password_confirmation"></Input>
            </FormItem>
            <FormItem class="bottom">
              <Button @click="submit('form')" long type="primary">重置密码</Button>
            </FormItem>
          </Form>
        </Card>
      </Col>
    </Row>
  </div>
</template>

<script>
  import {resetPassword} from "api/login";

  export default {
    name: "reset",
    data() {
      const validatePassCheck = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请再次输入新密码'));
        } else if (value !== this.form.password) {
          callback(new Error('两次输入的密码不匹配'));
        } else {
          callback();
        }
      };
      return {
        showAlert: false,
        form: {
          email: '',
          token: '',
          password: '',
          password_confirmation: ''
        },
        rules: {
          password: [],
          password_confirmation: [
            {validator: validatePassCheck, trigger: 'blur'}
          ],
        },
      }
    },
    methods: {
      submit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            resetPassword(this.form).then((response) => {
              this.showAlert = true;
              this.$refs[name].resetFields();
            })
          }
        })
      }
    },
    created() {
      this.form.email = this.$route.query.email;
      this.form.token = this.$route.params.token;
    },
    mounted() {
      particlesJS.load('particles-js', 'particles.json');
    }
  }
</script>

<style lang="scss" rel="stylesheet/scss" scoped>
  .row {
    padding-top: 160px;

    .bottom {
      margin-bottom: 0;
    }
  }

  #particles-js {
    position: absolute;
    width: 100%;
    height: 100%;
    background: {
      size: cover;
      color: #b61924;
      repeat: no-repeat;
      position: 50% 50%;
    }
  }
</style>
