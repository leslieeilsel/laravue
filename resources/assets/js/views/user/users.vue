<template>
  <div>
    <Card>
      <p class="btnGroup">
        <Button type="primary" @click="modal = true" icon="md-add">添加用户</Button>
        <Button type="error" @click="delAll" icon="md-trash">删除</Button>
        <Modal
          v-model="modal"
          @on-cancel="cancel"
          title="添加用户">
          <Form ref="formValidate" :model="form" :rules="ruleValidate" :label-width="80">
            <Form-item label="所属部门" prop="department_title">
              <Poptip trigger="click" placement="right" title="选择部门" width="340">
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
            <FormItem label="姓名" prop="name">
              <Input v-model="form.name" placeholder="可选项"></Input>
            </FormItem>
            <Form-item label="职位" prop="office">
              <Select v-model="form.office">
                <Option v-for="item in dict.office" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </Form-item>
            <FormItem label="邮箱" prop="email">
              <Input v-model="form.email" placeholder="可选项"></Input>
            </FormItem>
            <FormItem label="联系电话" prop="phone">
              <Input v-model="form.phone" placeholder="可选项"></Input>
            </FormItem>
            <FormItem label="角色分配" prop="group_id">
              <Select v-model="form.group_id" aria-label="">
                <Option v-for="item in roleList" :value="item.id" :label="item.name" :key="item.id">
                  <span>{{ item.name }}</span>
                  <span style="float:right;padding-right:15px;color:#ccc">{{ item.description }}</span>
                </Option>
              </Select>
            </FormItem>
            <FormItem label="用户名" prop="username">
              <Input v-model="form.username" placeholder="必填项"></Input>
            </FormItem>
            <FormItem label="密码" prop="password">
              <Input v-model="form.password" :type="passwordType" @on-focus="changePasswordType('password')" autocomplete="off" placeholder="必填项"/>
            </FormItem>
            <FormItem label="确认密码" prop="pwdCheck">
              <Input v-model="form.pwdCheck" :type="checkPasswordType" @on-focus="changePasswordType('pwdCheck')" autocomplete="off" placeholder="必填项"/>
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
        <Modal
          v-model="editModal"
          @on-cancel="cancel"
          title="编辑用户">
          <Form ref="editFormValidate" :model="editForm" :rules="editRuleValidate" :label-width="80">
            <Form-item label="所属部门" prop="department_title">
              <Poptip trigger="click" placement="right" title="选择部门" width="340">
                <div style="display:flex;">
                  <Input v-model="editForm.department_title" readonly style="margin-right:10px;" placeholder=""/>
                  <Button icon="md-trash" @click="clearSelectDepE">清空已选</Button>
                </div>
                <div slot="content" class="tree-bar">
                  <Tree :data="dataDep" :load-data="loadDataTree" @on-select-change="selectTree"></Tree>
                  <Spin size="large" fix v-if="dpLoading"></Spin>
                </div>
              </Poptip>
            </Form-item>
            <FormItem label="姓名" prop="name">
              <Input v-model="editForm.name" placeholder="可选项"></Input>
            </FormItem>
            <Form-item label="职位" prop="office">
              <Select v-model="editForm.office">
                <Option v-for="item in dict.office" :value="item.value" :key="item.value">{{ item.title }}</Option>
              </Select>
            </Form-item>
            <FormItem label="邮箱" prop="email">
              <Input v-model="editForm.email" placeholder="可选项"></Input>
            </FormItem>
            <FormItem label="联系电话" prop="phone">
              <Input v-model="editForm.phone" placeholder="可选项"></Input>
            </FormItem>
            <FormItem label="角色分配" prop="group_id">
              <Select v-model="editForm.group_id" aria-label="">
                <Option v-for="item in roleList" :value="item.id" :label="item.name" :key="item.id">
                  <span>{{ item.name }}</span>
                  <span style="float:right;padding-right:15px;color:#ccc">{{ item.description }}</span>
                </Option>
              </Select>
            </FormItem>
            <FormItem label="用户名" prop="username">
              <Input v-model="editForm.username" placeholder="必填项"></Input>
            </FormItem>
            <!-- <FormItem label="密码" prop="password">
              <Input v-model="editForm.password" :type="passwordType" @on-focus="changePasswordType('password')" autocomplete="off" placeholder="必填项"/>
            </FormItem>
            <FormItem label="确认密码" prop="pwdCheck">
              <Input v-model="editForm.pwdCheck" :type="checkPasswordType" @on-focus="changePasswordType('pwdCheck')" autocomplete="off" placeholder="必填项"/>
            </FormItem> -->
            <FormItem label="备注" prop="desc">
              <Input v-model="editForm.desc" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="可选项"></Input>
            </FormItem>
          </Form>
          <div slot="footer">
            <Button @click="handleReset('editFormValidate')" style="margin-left: 8px">重置</Button>
            <Button type="primary" @click="handleSubmitE('editFormValidate')" :loading="loading">提交</Button>
          </div>
        </Modal>
      </p>
      <Table type="selection" border :columns="columns" :data="nowData" @on-selection-change="showSelect" :loading="loadingTable"></Table>
      <Row type="flex" justify="end" class="page">
        <Page :total="dataCount" :page-size="pageSize" @on-change="changePage" @on-page-size-change="_nowPageSize"
              show-total show-sizer/>
      </Row>
    </Card>
  </div>
</template>
<script>
  import {registUser, getUsers, getUserDictData,deleteUserData,editRegistUser,getUser} from '../../api/user';
  import {initDepartment, loadDepartment} from '../../api/system';
  import {getRoles} from '../../api/role';
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
        if (this.form.password !== '' && value === '') {
          callback(new Error('确认密码不能为空'));
        } else if (this.form.password !== value) {
          callback(new Error('新密码和确认密码应相同'));
        } else {
          callback();
        }
      };
      return {
        pageSize: 10,   // 每页显示多少条
        dataCount: 0,   // 总条数
        pageCurrent: 1, // 当前页
        nowData: [],
        passwordType: 'text',
        checkPasswordType: 'text',
        loadingTable: true,
        loading: false,
        dpLoading: false,
        modal: false,
        editModal: false,
        selectDep: [],
        dataDep: [],
        dictName: {
          office: '职位',
        },
        dict: {
          office: [],
        },
        checkedDefaultRole: '',
        form: {
          username: '',
          name: '',
          email: '',
          office: '',
          phone: '',
          department_id: '',
          department_title: '',
          group_id: '',
          password: '',
          pwdCheck: '',
          desc: ''
        },editForm:{
          id: '',
          username: '',
          name: '',
          email: '',
          office: '',
          phone: '',
          department_id: '',
          department_title: '',
          group_id: '',
          // password: '',
          // pwdCheck: '',
          desc:''
        },
        ruleValidate: {
          name: [
            {required: true, message: '姓名不能为空', trigger: 'blur'}
          ],
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
            {required: true, message: '所属部门不能为空', trigger: 'change'}
          ],
          office: [
            {required: true, message: '职位不能为空', trigger: 'change', type: 'number'}
          ],
          group_id: [
            {required: true, message: '职位不能为空', trigger: 'change', type: 'number'}
          ]
        },
        editRuleValidate: {
          name: [
            {required: true, message: '姓名不能为空', trigger: 'blur'}
          ],
          username: [
            {required: true, message: '用户名不能为空', trigger: 'blur'}
          ],
          email: [
            {type: 'email', message: '邮箱格式不正确', trigger: 'blur'}
          ],
          // password: [
          //   {required: true, validator: pwdValidate, trigger: 'blur'}
          // ],
          // pwdCheck: [
          //   {required: true, validator: pwdCheckValidate, trigger: 'blur'}
          // ],
          department_title: [
            {required: true, message: '所属部门不能为空', trigger: 'change'}
          ],
          office: [
            {required: true, message: '职位不能为空', trigger: 'change', type: 'number'}
          ],
          group_id: [
            {required: true, message: '职位不能为空', trigger: 'change', type: 'number'}
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
            title: '职位',
            key: 'office'
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
          },
          {
            title: '操作',
            key: 'action',
            width: 150,
            fixed: 'right',
            align: 'center',
            render: (h, params) => {
              return h('div', [
                h('Button', {
                  props: {
                    type: 'primary',
                    size: 'small'
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      console.log();
                      this.editForm.id=params.row.id;
                      this.editForm.username=params.row.username;
                      this.editForm.name=params.row.name;
                      this.editForm.email=params.row.email;
                      this.editForm.phone=params.row.phone;                  
                      this.editForm.department_title=params.row.department;
                      this.editForm.password=params.row.password;
                      this.editForm.pwdCheck=params.row.pwdCheck;
                      this.editForm.desc=params.row.desc;
                      getUser({id:params.row.id}).then((data) => {
                        this.editForm.department_id=data.result.department_id;
                        this.editForm.group_id=data.result.group_id;
                        this.editForm.office=data.result.office;
                      })
                      this.editModal = true;
                    }
                  }
                }, '编辑')
              ]);
            }
          }
        ],
        data: [],
        rowSelection,
        roleList: [],
        drop: false,
        dropDownContent: "展开",
        dropDownIcon: "ios-arrow-down",
        selectCount: 0, // 多选计数
        selectList: [], // 多选数据
      }
    },
    mounted() {
      this.init();
      getUsers().then((data) => {
        this.data = data.result;
        //分页显示所有数据总数
        this.dataCount = this.data.length;
        //循环展示页面刚加载时需要的数据条数
        this.nowData = [];
        for (let i = 0; i < this.pageSize; i++) {
          if (this.data[i]) {
            this.nowData.push(this.data[i]);
          }
        }
        this.loadingTable = false;
      });
      getRoles().then((data) => {
        this.roleList = data.result;
        this.roleList.forEach(e => {
          if (e.is_default === 1) {
            this.checkedDefaultRole = e.id;
            this.form.group_id = this.checkedDefaultRole;
          }
        });
      });
    },
    methods: {
      init() {
        this.initDepartmentTreeData();
        this.getDictData();
      },
      getDictData() {
        getUserDictData(this.dictName).then(res => {
          if (res.result) {
            this.dict = res.result;
          }
        });
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
                this.init();
              });
            });
          } else {
            this.$Message.error('发生错误!');
          }
        })
      },handleSubmitE(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            editRegistUser(this.editForm).then((response) => {
              this.loading = false;
              this.$Message.success('修改成功');
              this.$refs[name].resetFields();
              this.editModal = false;
              this.loadingTable = true;
              getUsers().then((data) => {
                this.data = data.result;
                this.loadingTable = false;
                this.init();
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
        this.form.group_id = this.checkedDefaultRole;
      },
      clearSelectDep() {
        this.form.department_id = "";
        this.form.department_title = "";
      },
      clearSelectDepE() {
        this.editForm.department_id = "";
        this.editForm.department_title = "";
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
      changePasswordType(name) {
        if (name === 'password') {
          this.passwordType = 'password';
        } else {
          this.checkPasswordType = 'password';
        }
      },
      changePage(index) {
        //需要显示开始数据的index,(因为数据是从0开始的，页码是从1开始的，需要-1)
        let _start = (index - 1) * this.pageSize;
        //需要显示结束数据的index
        let _end = index * this.pageSize;
        //截取需要显示的数据
        this.nowData = this.data.slice(_start, _end);
        //储存当前页
        this.pageCurrent = index;
      },
      _nowPageSize(index) {
        //实时获取当前需要显示的条数
        this.pageSize = index;
        this.loadingTable = true;
        this.nowData = [];
        for (let i = 0; i < this.pageSize; i++) {
          if (this.data[i]) {
            this.nowData.push(this.data[i]);
          }
        }
        this.loadingTable = false;
      },//删除
      delAll() {
        if (this.selectCount <= 0) {
          this.$Message.warning("您还未选择要删除的数据");
          return;
        }
        this.$Modal.confirm({
          title: "确认删除",
          loading: true,
          content: "您确认要删除所选的 " + this.selectCount + " 条数据？",
          onOk: () => {
            let ids = "";
            this.selectList.forEach(function (e) {
              ids += e.id + ",";
            });
            ids = ids.substring(0, ids.length - 1);
            // 批量删除
            deleteUserData(ids).then(res => {
              this.$Modal.remove();
              if (res.result === true) {
                this.$Message.success("操作成功");
                this.getDataList();
                getUsers().then((data) => {
                  this.data = data.result;
                  this.loadingTable = false;
                  this.init();
                });
              }
            });
          }
        });
      },
      showSelect(e) {
        this.selectList = e;
        this.selectCount = e.length;
      }
    }
  }
</script>
