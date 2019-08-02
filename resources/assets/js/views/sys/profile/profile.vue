<template>
  <Card>
    <Tabs value="baseInfo">
      <TabPane label="基础信息" name="baseInfo">
        <Form ref="userForm" :model="userForm" :label-width="100" label-position="right">
          <FormItem label="账号名：">
            <span>{{userForm.username}}</span>
          </FormItem>
          <FormItem label="姓名：">
            <span>{{userForm.name}}</span>
          </FormItem>
          <FormItem label="手机号：">
            <span>{{userForm.phone}}</span>
          </FormItem>
          <FormItem label="邮箱：">
            <span>{{userForm.email}}</span>
          </FormItem>
          <FormItem label="所属部门：">
            <span>{{userForm.department_name}}</span>
          </FormItem>
          <FormItem label="注册时间：">
            <span>{{userForm.created_at}}</span>
          </FormItem>
          <FormItem label="备注：">
            <span>{{userForm.desc}}</span>
          </FormItem>
        </Form>
        <Spin size="large" fix v-if="infoLoading"></Spin>
      </TabPane>
      <TabPane label="安全设置" name="safeSetting">
        <Col span="20">
          <p>账户密码</p>
        </Col>
        <Col span="4">
          <a class="right" @click="modal1 = true">修改</a>
          <Modal
            v-model="modal1"
            title="修改密码">
            <Form ref="formValidate" :model="formResetPassword" :rules="ruleInline" :label-width="90">
              <FormItem label="原密码" prop="oldPassword">
                <Input type="password" v-model="formResetPassword.oldPassword" placeholder="请输入现在使用的密码"/>
              </FormItem>
              <FormItem label="新密码" prop="newPassword">
                <Input type="password" v-model="formResetPassword.newPassword" placeholder="请输入新密码"/>
              </FormItem>
              <FormItem label="确认新密码" prop="confirmNewPassword">
                <Input type="password" v-model="formResetPassword.confirmNewPassword" placeholder="请再次输入新密码"/>
              </FormItem>
            </Form>
            <div slot="footer">
              <Button @click="handleReset('formValidate')" style="margin-left: 8px">重置</Button>
              <Button type="primary" @click="handleSubmit('formValidate')" :loading="loading">提交</Button>
            </div>
          </Modal>
        </Col>
        <Divider/>
      </TabPane>
    </Tabs>
  </Card>
</template>
<script>
  import './profile.css';
  import {resetPassword} from 'api/user';
  import {getAllDepartment} from 'api/system';

  export default {
    data() {
      const pwdValidate = (rule, value, callback) => {
        this.$refs.formValidate.validateField('confirmNewPassword');
        callback();
      };
      const pwdCheckValidate = (rule, value, callback) => {
        if (this.formResetPassword.newPassword !== '' && value === '') {
          callback(new Error('确认密码不能为空'));
        } else if (this.formResetPassword.confirmNewPassword !== value) {
          callback(new Error('新密码和确认密码应相同'));
        } else {
          callback();
        }
      };
      return {
        infoLoading: false,
        loading: false,
        accessToken: {},
        userForm: {
          username: '',
          name: '',
          phone: '',
          email: '',
          department_name: '',
          desc: '',
          created_at: '',
        },
        modal1: false,
        formResetPassword: {
          password: '',
          newPassword: '',
          confirmNewPassword: '',
        },
        ruleInline: {
          oldPassword: [
            {required: true, message: '原密码不能为空', trigger: 'blur'},
          ],
          newPassword: [
            {required: true, validator: pwdValidate, trigger: 'blur'}
          ],
          confirmNewPassword: [
            {required: true, validator: pwdCheckValidate, trigger: 'blur'}
          ],
        },
        departments: {}
      }
    },
    methods: {
      init() {
        this.getDepartments();
      },
      getDepartments() {
        this.infoLoading = true;
        getAllDepartment().then(res => {
          if (res.result) {
            this.departments = res.result;
            let departmentId = this.$store.getters.user.department_id;
            this.$store.getters.user.department_name = this.departments[departmentId];
            this.userForm = this.$store.getters.user;
            this.infoLoading = false;
          }
        });
      },
      handleReset(name) {
        this.$refs[name].resetFields();
      },
      handleSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            resetPassword(this.formResetPassword).then((res) => {
              if (res.result) {
                this.loading = false;
                this.$Message.success('修改成功，请重新登陆！');
                this.$refs[name].resetFields();
                this.modal1 = false;
                this.$store.dispatch('logout').then(() => this.$router.push('login'));
              } else {
                this.$Message.error('原密码错误！');
                this.loading = false;
              }
            });
          } else {
            this.$Message.error('发生错误！');
          }
        })
      },
    },
    mounted() {
      this.init();
    }
  }
</script>
