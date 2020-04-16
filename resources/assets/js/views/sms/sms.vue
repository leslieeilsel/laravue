<template>
  <div id="background1">
    <div id="form">
      <Form ref="form" :model="form" :rules="ruleCustom" :label-width="0">
        <FormItem label="" prop="phone" style="margin-bottom: 15px;">
          <Input size="small" type="text" v-model="form.phone" placeholder="输入您的手机号"></Input>
        </FormItem>
        <FormItem label="" prop="code" style="margin-bottom: 15px;">
          <Input @on-search="sendMessage" size="small" type="text" search :enter-button="sendStatus"
                 placeholder="请输入验证码" v-model="form.code"/>
          <span v-if="show" style="color: #fff;font-size: 12px;">{{count}} 秒后可重新发送</span>
        </FormItem>
        <FormItem style="text-align: center;">
          <Button :disabled="loginBtn" size="small" @click="handleSubmit('form')">点击登录</Button>
        </FormItem>
      </Form>
    </div>
  </div>
</template>
<script>
  import {sendSms,savePhone} from "../../api/message";

  export default {
    data() {
      const validatePhone = (rule, value, callback) => {
        if (!value) {
          this.$Message.error('手机号不能为空');
        } else if (!/^1[34578]\d{9}$/.test(value)) {
          this.$Message.error('手机号格式不正确');
        } else {
          callback();
        }
      };
      return {
        loginBtn: true,
        show: false,
        count: '',
        timer: null,
        form: {
          phone: '',
          code: '',
        },
        sendStatus: '发送',
        code: null,
        ruleCustom: {
          phone: [
            {required: true, validator: validatePhone, trigger: 'blur'}
          ],
        }
      }
    },
    methods: {
      sendMessage(value) {
        if (this.form.phone && this.sendStatus === '发送') {
          sendSms(this.form).then(res => {
            if (res.data.xml.returnstatus === 'Success') {
              this.sendStatus = '已发送';
              this.getCode();
              this.code = res.data.code;
              this.loginBtn = false;
            } else {
              this.$Message.error(res.data.xml.message);
            }
          })
        } else {
          this.$Message.error('请填写手机号码');
        }
      },
      getCode() {
        const TIME_COUNT = 60;
        if (!this.timer) {
          this.count = TIME_COUNT;
          this.show = true;
          this.timer = setInterval(() => {
            if (this.count > 0 && this.count <= TIME_COUNT) {
              this.count--;
            } else {
              this.show = false;
              clearInterval(this.timer);
              this.timer = null;
              this.sendStatus = '发送';
            }
          }, 1000)
        }
      },
      handleSubmit(name) {
        if (this.form.code && Number(this.form.code) === this.code) {
          savePhone({'phone_number': this.form.phone}).then(res => {
            if (res.data.result) {
              this.$Message.success('验证通过');
              this.form.phone = '';
              this.form.code = '';
              this.loginBtn = true;
            }
          })
        } else {
          this.$Message.error('验证码错误');
        }
      },
    }
  }
</script>

<style>
  #background1 {
    position: relative;
    width: 100%;
    height: 100%;
    background-image: url('/images/background.jpg');
    background-repeat: no-repeat repeat;
    background-size: 100% 100%;
    background-attachment: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
  }

  #form {
    margin: 0 auto;
    /*max-width: 280px;*/
    position: absolute;
    bottom: 15%;
    right: 23%;
    left: 23%;
  }
</style>
