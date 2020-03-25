<template>
  <div>
    <Card>
      <Row>
        <Form :label-width="70" :model="searchForm" class="search-form" inline ref="searchForm">
          <FormItem label="所属部门" prop="department_title">
            <Poptip placement="bottom" title="选择部门" trigger="click" width="340">
              <div style="display:flex;">
                <Input placeholder="" readonly style="margin-right:10px;" v-model="searchForm.department_title"/>
              </div>
              <div class="tree-bar" slot="content">
                <Tree :data="dataDep" :load-data="loadDataTree" @on-select-change="selectTreeSearch"></Tree>
                <Spin fix size="large" v-if="dpLoading"></Spin>
              </div>
            </Poptip>
          </FormItem>
          <FormItem label="姓名" prop="name">
            <Input clearable placeholder="支持模糊搜索" style="width: 200px" v-model="searchForm.name"/>
          </FormItem>
          <span v-if="drop">
            <Form-item label="角色" prop="group_id">
              <Select aria-label="" style="width: 200px" v-model="searchForm.group_id">
                <Option key='-1' value='-1'>全部</Option>
                <Option :key="item.id" :label="item.name" :value="item.id" v-for="item in roleList">
                  <span>{{ item.name }}</span>
                  <span style="float:right;padding-right:15px;color:#ccc">{{ item.description }}</span>
                </Option>
              </Select>
            </Form-item>
            <Form-item label="用户名" prop="username">
              <Input clearable placeholder="支持模糊搜索" style="width: 200px" v-model="searchForm.username"/>
            </Form-item>
          </span>
          <FormItem class="br" style="margin-left:-70px;">
            <Button @click="getUserList" icon="ios-search" type="primary">搜索</Button>
            <Button @click="handleResetSearch">重置</Button>
            <a @click="dropDown" class="drop-down">
              {{dropDownContent}}
              <Icon :type="dropDownIcon"></Icon>
            </a>
          </FormItem>
        </Form>
      </Row>
      <p class="btnGroup">
        <Button @click="add" icon="md-add" type="primary">添加用户</Button>
        <Button @click="delAll" icon="md-trash">删除</Button>
        <Modal
          :title="modalTitle"
          @on-cancel="cancel"
          v-model="modal">
          <Form :label-width="80" :model="form" :rules="ruleValidate" ref="formValidate">
            <Form-item label="所属部门" prop="department_title">
              <Poptip placement="right" title="选择部门" trigger="click" width="400">
                <div style="display:flex;">
                  <Input placeholder="" readonly style="margin-right:10px;" v-model="form.department_title"/>
                  <Button @click="clearSelectDep" icon="md-trash">清空已选</Button>
                </div>
                <div class="tree-bar" slot="content">
                  <Tree :data="dataDep" :load-data="loadDataTree" @on-select-change="selectTree"></Tree>
                  <Spin fix size="large" v-if="dpLoading"></Spin>
                </div>
              </Poptip>
            </Form-item>
            <FormItem label="姓名" prop="name">
              <Input placeholder="可选项" v-model="form.name"></Input>
            </FormItem>
            <Form-item label="职位" prop="office">
              <Select v-model="form.office">
                <Option :key="item.value" :value="item.value" v-for="item in dict.office">{{ item.title }}</Option>
              </Select>
            </Form-item>
            <FormItem label="邮箱" prop="email">
              <Input placeholder="可选项" v-model="form.email"></Input>
            </FormItem>
            <FormItem label="联系电话" prop="phone">
              <Input placeholder="可选项" v-model="form.phone"></Input>
            </FormItem>
            <FormItem label="角色分配" prop="group_id">
              <Select aria-label="" v-model="form.group_id">
                <Option :key="item.id" :label="item.name" :value="item.id" v-for="item in roleList">
                  <span>{{ item.name }}</span>
                  <span style="float:right;padding-right:15px;color:#ccc">{{ item.description }}</span>
                </Option>
              </Select>
            </FormItem>
            <FormItem label="用户名" prop="username">
              <Input placeholder="必填项" v-model="form.username"></Input>
            </FormItem>
            <FormItem label="密码" prop="password">
              <Input :type="passwordType" @on-focus="changePasswordType('password')" autocomplete="off"
                     placeholder="必填项" v-model="form.password"/>
            </FormItem>
            <FormItem label="确认密码" prop="pwdCheck">
              <Input :type="checkPasswordType" @on-focus="changePasswordType('pwdCheck')" autocomplete="off"
                     placeholder="必填项" v-model="form.pwdCheck"/>
            </FormItem>
            <FormItem label="备注" prop="desc">
              <Input :autosize="{minRows: 2,maxRows: 5}" placeholder="可选项" type="textarea" v-model="form.desc"></Input>
            </FormItem>
          </Form>
          <div slot="footer">
            <Button @click="handleReset('formValidate')" style="margin-left: 8px" v-if="showResetButton">重置</Button>
            <Button :loading="loading" @click="handleSubmit('formValidate')" type="primary">保存</Button>
          </div>
        </Modal>
      </p>
      <Table :columns="columns" :data="data" :loading="loadingTable" @on-selection-change="showSelect" border size="small"
             type="selection"></Table>
      <Row class="page" justify="end" type="flex">
        <Page
          :current="searchForm.pageNumber"
          :page-size="searchForm.pageSize"
          :total="dataCount"
          @on-change="changePage"
          @on-page-size-change="changePageSize"
          show-total/>
      </Row>
    </Card>
  </div>
</template>
<script>
  import {deleteUserData, editRegistUser, getUserDictData, getUsers, registUser} from '../../api/user';
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
    data: function () {
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
        pwdValidate,
        pwdCheckValidate,
        showResetButton: false,
        passwordType: 'text',
        checkPasswordType: 'text',
        loadingTable: true,
        loading: false,
        dpLoading: false,
        modal: false,
        btnDisable: true,
        modalTitle: '',
        selectDep: [],
        dataDep: [],
        dictName: {
          office: '职位',
        },
        dict: {
          office: [],
        },
        checkedDefaultRole: 0,
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
            {required: true, message: '角色不能为空', trigger: 'change', type: 'number'}
          ]
        },
        columns: [
          {
            type: 'selection',
            width: 60,
            fixed: 'left',
            align: 'center'
          },
          {
            type: 'index',
            width: 70,
            align: 'center',
            fixed: 'left',
            render: (h, params) => {
              return h('span', params.index + (this.searchForm.pageNumber - 1) * this.searchForm.pageSize + 1);
            }
          },
          {
            title: '姓名',
            key: 'name',
            width: 150,
            fixed: 'left',
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
            key: 'username',
            width: 130
          },
          {
            title: '所属部门',
            key: 'department_title',
            width: 150
          },
          {
            title: '角色',
            key: 'group',
            width: 150
          },
          {
            title: '职位',
            key: 'office_name',
            width: 100
          },
          {
            title: '联系电话',
            key: 'phone',
            width: 150
          },
          {
            title: '邮箱',
            key: 'email',
            width: 150
          },
          {
            title: '创建时间',
            key: 'created_at',
            sortable: true,
            width: 150
          },
          {
            title: '最近登录时间',
            key: 'last_login',
            sortable: true,
            width: 150
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
                      this.edit(params.row);
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
        searchForm: {
          name: '',
          username: '',
          department_id: '',
          group_id: '',
          department_title: '',
          pageNumber: 1, // 当前页数
          pageSize: 10, // 页面大小
        },
        dataCount: 0,   // 总条数
        modalType: 0
      }
    },
    mounted() {
      this.init();
    },
    methods: {
      init() {
        this.initDepartmentTreeData();
        this.getDictData();
        this.getUserList();
        this.getDefaultRole();
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
            if (this.modalType === 0) {
              registUser(this.form).then((response) => {
                this.loading = false;
                this.$Message.success('创建成功');
                this.$refs[name].resetFields();
                this.modal = false;
                this.getUserList();
              });
            } else if (this.modalType === 1) {
              editRegistUser(this.form).then((res) => {
                this.loading = false;
                if (res.result === true) {
                  this.$Message.success('修改成功');
                  this.modal = false;
                  this.getUserList();
                } else {
                  this.$Message.error('修改失败');
                }
              });
            }
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
      add() {
        this.form.group_id = this.checkedDefaultRole;
        this.modalType = 0;
        this.modalTitle = "添加用户";
        this.showResetButton = true;
        this.ruleValidate.password = [{required: true, validator: this.pwdValidate, trigger: 'blur'}];
        this.$refs.formValidate.$children[7].$el.className = 'ivu-form-item ivu-form-item-required';
        this.ruleValidate.pwdCheck = [{required: true, validator: this.pwdCheckValidate, trigger: 'blur'}];
        this.$refs.formValidate.$children[8].$el.className = 'ivu-form-item ivu-form-item-required';
        this.$refs.formValidate.resetFields();
        this.modal = true;
      },
      edit(v) {
        this.modalType = 1;
        this.modalTitle = "编辑用户";
        this.showResetButton = false;
        this.ruleValidate.password = [{required: false, validator: this.pwdValidate, trigger: 'blur'}];
        this.$refs.formValidate.$children[7].$el.className = 'ivu-form-item';
        this.ruleValidate.pwdCheck = [{required: false, validator: this.pwdCheckValidate, trigger: 'blur'}];
        this.$refs.formValidate.$children[8].$el.className = 'ivu-form-item';
        this.$refs.formValidate.resetFields();
        // 转换null为""
        for (let attr in v) {
          if (v[attr] === null) {
            v[attr] = "";
          }
        }
        let str = JSON.stringify(v);
        this.form = JSON.parse(str);
        this.modal = true;
      },
      selectTreeSearch(v) {
        if (v.length > 0) {
          // 转换null为""
          for (let attr in v[0]) {
            if (v[0][attr] === null) {
              v[0][attr] = "";
            }
          }
          let str = JSON.stringify(v[0]);
          let data = JSON.parse(str);
          this.searchForm.department_id = data.id;
          this.searchForm.department_title = data.title;
        }
      },
      changePasswordType(name) {
        if (name === 'password') {
          this.passwordType = 'password';
        } else {
          this.checkPasswordType = 'password';
        }
      },
      changePage(v) {
        this.searchForm.pageNumber = v;
        this.getUserList();
      },
      changePageSize(v) {
        this.searchForm.pageSize = v;
        this.getUserList();
      },
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
                this.getUserList();
              }
            });
          }
        });
      },
      showSelect(e) {
        this.selectList = e;
        this.selectCount = e.length;
      },
      getUserList() {
        this.passwordType = this.checkPasswordType = 'text';
        this.loadingTable = true;
        getUsers(this.searchForm).then(res => {
          this.loadingTable = false;
          this.data = res.result;
          this.dataCount = res.total;
        });
      },
      getDefaultRole() {
        getRoles().then((data) => {
          this.roleList = data.result;
          this.roleList.forEach(e => {
            if (e.is_default === 1) {
              this.checkedDefaultRole = e.id;
            }
          });
        });
      },
      handleResetSearch() {
        this.$refs.searchForm.resetFields();
        this.dataCount = 0;
        this.searchForm.pageNumber = 1;
        this.searchForm.pageSize = 10;
        this.getUserList();
      },
      dropDown() {
        if (this.drop) {
          this.dropDownContent = "展开";
          this.dropDownIcon = "ios-arrow-down";
        } else {
          this.dropDownContent = "收起";
          this.dropDownIcon = "ios-arrow-up";
        }
        this.drop = !this.drop;
      }
    }
  }
</script>
