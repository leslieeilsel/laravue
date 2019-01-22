<template>
  <div>
    <Card>
      <p class="btnGroup">
        <Button type="primary" @click="modal = true" icon="md-add">添加用户</Button>
        <Button type="error" disabled icon="md-trash">删除</Button>
        <Modal
          v-model="modal"
          @on-cancel="cancel"
          title="添加用户">
          <Form ref="formValidate" :model="form" :rules="ruleValidate" :label-width="80">
            <FormItem label="用户名" prop="username">
              <Input v-model="form.username" placeholder="必填项"></Input>
            </FormItem>
            <FormItem label="姓名" prop="name">
              <Input v-model="form.name" placeholder="可选项"></Input>
            </FormItem>
            <FormItem label="邮箱" prop="email">
              <Input v-model="form.email" placeholder="可选项"></Input>
            </FormItem>
            <FormItem label="联系电话" prop="phone">
              <Input v-model="form.phone" placeholder="可选项"></Input>
            </FormItem>
            <Form-item label="所属部门" prop="department_title">
              <Poptip trigger="click" placement="right" title="选择部门" width="250">
                <div style="display:flex;">
                  <Input v-model="form.department_title" readonly style="margin-right:10px;" placeholder=""/>
                  <Button icon="md-trash" @click="clearSelectDep">清空已选</Button>
                </div>
                <div slot="content" class="tree-bar">
                  <Tree :data="dataDep" :load-data="loadDataTree" @on-select-change="selectTree"></Tree>
                  <Spin size="large" fix v-if="dpLoading"></Spin>
                </div>
              </Poptip>
            </Form-item>
            <FormItem label="角色分配" prop="group_id">
              <Select v-model="form.group_id" aria-label="">
                <Option v-for="item in roleList" :value="item.id" :label="item.name" :key="item.id">
                  <span>{{ item.name }}</span>
                  <span style="float:right;padding-right:15px;color:#ccc">{{ item.description }}</span>
                </Option>
              </Select>
            </FormItem>
            <FormItem label="密码" prop="password">
              <Input v-model="form.password" type="password" placeholder="必填项"/>
            </FormItem>
            <FormItem label="确认密码" prop="pwdCheck">
              <Input v-model="form.pwdCheck" type="password" placeholder="必填项"/>
            </FormItem>
            <FormItem label="备注" prop="desc">
              <Input v-model="form.desc" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="可选项"></Input>
            </FormItem>
          </Form>
          <div slot="footer">
            <Button @click="handleReset('formValidate')" style="margin-left: 8px">重置</Button>
            <Button type="primary" @click="handleSubmit('formValidate')" :loading="loading">提交</Button>
          </div>
        </Modal>
      </p>
      <Table border :columns="columns" :data="data" :loading="loadingTable"></Table>
    </Card>
  </div>
</template>
<script>
  import {registUser, getUsers} from 'api/user';
  import {initDepartment, loadDepartment} from 'api/system';
  import {getRoles} from 'api/role';
  import {getDeptSelecter} from 'api/department';
  import './users.css';

  const rowSelection = {
    onChange: (selectedRowKeys, selectedRows) => {
      console.log(`selectedRowKeys: ${selectedRowKeys}`, 'selectedRows: ', selectedRows);
    },
    onSelect: (record, selected, selectedRows) => {
      console.log(record, selected, selectedRows);
    },
    onSelectAll: (selected, selectedRows, changeRows) => {
      console.log(selected, selectedRows, changeRows);
    },
  };
  export default {
    data() {
      const pwdValidate = (rule, value, callback) => {
        this.$refs.formValidate.validateField('pwdCheck');
        callback();
      };
      const pwdCheckValidate = (rule, value, callback) => {
        if (this.form.password != '' && value == '') {
          callback(new Error('确认密码不能为空'));
        } else if (this.form.password != value) {
          callback(new Error('新密码和确认密码应相同'));
        } else {
          callback();
        }
      };
      return {
        loadingTable: true,
        loading: false,
        dpLoading: false,
        modal: false,
        selectDep: [],
        dataDep: [],
        form: {
          username: '',
          name: '',
          email: '',
          phone: '',
          department_id: '',
          department_title: '',
          group_id: '',
          password: '',
          pwdCheck: '',
          desc: ''
        },
        ruleValidate: {
          username: [
            {required: true, message: '用户名不能为空', trigger: 'blur'}
          ],
          email: [
            {type: 'email', message: '邮箱格式不正确', trigger: 'blur'}
          ],
          password: [
            {required: true, validator: pwdValidate, trigger: 'blur'}
          ],
          pwdCheck: [
            {required: true, validator: pwdCheckValidate, trigger: 'blur'}
          ],
          department_title: [
            {required: true, message: '所属部门不能为空', trigger: 'blur'}
          ]
        },
        columns: [
          {
            type: 'selection',
            width: 60,
            align: 'center'
          },
          {
            title: '姓名',
            key: 'name',
            render: (h, params) => {
              return h('div', [
                h('Icon', {
                  props: {
                    type: 'person'
                  }
                }),
                h('strong', params.row.name)
              ]);
            }
          },
          {
            title: '用户名',
            key: 'username'
          },
          {
            title: '所属部门',
            key: 'department'
          },
          {
            title: '角色',
            key: 'group'
          },
          {
            title: '邮箱',
            key: 'email'
          },
          {
            title: '创建时间',
            key: 'created_at',
            sortable: true,
          },
          {
            title: '最近登录时间',
            key: 'last_login',
            sortable: true,
          }
        ],
        data: [],
        rowSelection,
        roleList: [],
        drop: false,
        dropDownContent: "展开",
        dropDownIcon: "ios-arrow-down",
      }
    },
    mounted() {
      this.init();
      getUsers().then((data) => {
        this.data = data.result;
        this.loadingTable = false;
      });
      getRoles().then((data) => {
        this.roleList = data.result;
      });
    },
    methods: {
      init() {
        this.initDepartmentTreeData();
      },
      remove(index) {
        this.data.splice(index, 1);
      },
      handleSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            registUser(this.form).then((response) => {
              this.loading = false;
              this.$Message.success('创建成功');
              this.$refs[name].resetFields();
              this.modal = false;
              this.loadingTable = true;
              getUsers().then((data) => {
                this.data = data.result;
                this.loadingTable = false;
              });
            });
          } else {
            this.$Message.error('发生错误!');
          }
        })
      },
      handleReset(name) {
        this.$refs[name].resetFields();
      },
      cancel() {
        this.$refs.formValidate.resetFields();
      },
      clearSelectDep() {
        this.form.department_id = "";
        this.form.department_title = "";
      },
      initDepartmentTreeData() {
        initDepartment().then(res => {
          res.result.forEach(function (e) {
            if (e.is_parent) {
              e.loading = false;
              e.children = [];
            }
            if (e.status === 0) {
              e.title = "[已禁用] " + e.title;
              e.disabled = true;
            }
          });
          this.dataDep = res.result;
        });
      },
      loadDataTree(item, callback) {
        loadDepartment(item.id).then(res => {
          res.result.forEach(function (e) {
            if (e.is_parent) {
              e.loading = false;
              e.children = [];
            }
            if (e.status === 0) {
              e.title = "[已禁用] " + e.title;
              e.disabled = true;
            }
          });
          callback(res.result);
        });
      },
      selectTree(v) {
        if (v.length > 0) {
          // 转换null为""
          for (let attr in v[0]) {
            if (v[0][attr] === null) {
              v[0][attr] = "";
            }
          }
          let str = JSON.stringify(v[0]);
          let data = JSON.parse(str);
          this.form.department_id = data.id;
          this.form.department_title = data.title;
        }
      },
    }
  }
</script>
