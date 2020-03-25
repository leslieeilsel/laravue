<template>
  <div>
    <div id="particles-js"></div>
    <Row class-name="row" justify="center" type="flex">
      <Col span="6">
        <Card>
          <p slot="title">{{ $t('login.retrievePassword') }}</p>
          <Alert closable show-icon type="success" v-show="showAlert">
            我们已将重置链接发送到您的邮箱，请注意查收，此链接30分钟内有效。
          </Alert>
          <Form :model="form" :rules="rules" label-position="top" ref="form">
            <FormItem :label="$t('login.email')" prop="email">
              <Input v-model="form.email"></Input>
            </FormItem>
            <FormItem class="bottom">
              <Button :loading="loading" @click="submit('form')" long type="primary">{{ $t('Next Step') }}</Button>
            </FormItem>
          </Form>
        </Card>
      </Col>
    </Row>
  </div>
</template>

<script>
  import {sendEmail} from "api/login";

  export default {
    name: "email",
    data() {
      return {
        loading: false,
        showAlert: false,
        form: {
          email: ''
        },
        rules: {
          email: [
            {required: true, message: '请填写邮箱地址', trigger: 'blur'},
            {type: 'email', message: '请填写正确的邮箱地址', trigger: 'blur'}
          ],
        }
      }
    },
    methods: {
      submit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            sendEmail(this.form.email).then((response) => {
              this.loading = false;
              this.showAlert = true;
            }).catch((error) => {

            });
          }
        })
      }
    },
    mounted() {
      particlesJS.load('particles-js', 'particles.json');
    }
  }
</script>

<style lang="scss" rel="stylesheet/scss" scoped>
  .row {
    padding-top: 180px;

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
