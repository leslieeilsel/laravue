<template>
  <div>
    <Card>
      <p class="btnGroup">
        <Button @click="modal = true" icon="md-add" type="primary">添加角色</Button>
        <Button @click="delAll" icon="md-trash">删除</Button>
        <!-- 菜单权限 -->
        <Modal
          :mask-closable="false"
          :width="700"
          @on-cancel="cancel"
          title="添加角色"
          v-model="modal">
          <Form :label-width="150" :model="form" :rules="ruleValidate" ref="formValidate">
            <FormItem label="角色名称：" prop="name">
              <Input placeholder="必填项" v-model="form.name"></Input>
            </FormItem>
            <FormItem label="设置为默认角色：" prop="is_default">
              <i-switch :false-value="0" :true-value="1" size="large" v-model="form.is_default">
                <span slot="open">是</span>
                <span slot="close">否</span>
              </i-switch>
            </FormItem>
            <FormItem label="备注：" prop="description">
              <Input placeholder="可选项" type="textarea" v-model="form.description"></Input>
            </FormItem>
          </Form>
          <div slot="footer">
            <Button @click="handleReset('formValidate')" style="margin-left: 8px">重置</Button>
            <Button :loading="loading" @click="handleSubmit('formValidate')" type="primary">提交</Button>
          </div>
        </Modal>
        <Modal
          :width="700"
          @on-cancel="cancel"
          title="编辑角色"
          v-model="editModal">
          <Form :label-width="150" :model="editForm" :rules="editRuleValidate" ref="editFormValidate">
            <FormItem label="角色名称" prop="name">
              <Input placeholder="必填项" v-model="editForm.name"></Input>
            </FormItem>
            <FormItem label="是否设置为默认角色" prop="is_default">
              <i-switch :false-value="0" :true-value="1" size="large" v-model="editForm.is_default">
                <span slot="open">是</span>
                <span slot="close">否</span>
              </i-switch>
            </FormItem>
            <FormItem label="备注" prop="description">
              <Input placeholder="可选项" type="textarea" v-model="editForm.description"></Input>
            </FormItem>
          </Form>
          <div slot="footer">
            <Button @click="handleReset('editFormValidate')" style="margin-left: 8px">重置</Button>
            <Button :loading="loading" @click="handleSubmitE('editFormValidate')" type="primary">提交</Button>
          </div>
        </Modal>
        <Modal
          :width="700"
          title="设置菜单权限"
          v-model="treeModal">
          <Tree :data="treeData" multiple ref="tree" show-checkbox></Tree>
          <div slot="footer">
            <Button :loading="treeSubmitLoading" @click="treeSubmit()" type="primary">提交</Button>
          </div>
        </Modal>
        <!-- 数据权限 -->
        <Modal :mask-closable='false' :styles="{top: '30px'}" :title="modalTitle" :width="700" class="depModal"
               v-model="depModalVisible">
          <Form :label-width="100">
            <FormItem label="数据范围：">
              <Select v-model="dataType">
                <Option :value="0">全部数据权限</Option>
                <Option :value="1">自定义数据权限</Option>
                <Option :value="2">仅自己</Option>
              </Select>
            </FormItem>
          </Form>
          <Alert show-icon v-if="dataType===0">
            默认可查看全部数据
          </Alert>
          <Alert show-icon v-if="dataType===1">
            请点击选择下方数据
          </Alert>
          <Alert show-icon v-if="dataType===2">
            仅能查看自己添加的数据
          </Alert>
          <div style="margin-top:15px" v-if="dataType===1">
            <Tree :data="depData" ref="depTree" show-checkbox style="margin-top:15px"></Tree>
            <Spin size="large" v-if="depTreeLoading"></Spin>
          </div>
          <div slot="footer">
            <Button @click="depModalVisible=false" type="text">取消</Button>
            <Button :loading="submitDepLoading" @click="submitDepEdit" type="primary">提交</Button>
          </div>
        </Modal>
      </p>
      <Table :columns="columns" :data="nowData" :loading="loadingTable" @on-selection-change="showSelect" border
             size="small"></Table>
      <Row class="page" justify="end" type="flex">
        <Page :current="pageCurrent" :page-size="pageSize" :total="dataCount" @on-change="changePage"
              @on-page-size-change="_nowPageSize"
              show-sizer show-total/>
      </Row>
    </Card>
  </div>
</template>
<script>
  import './users.css';
  import {
    add,
    deleteRoleData,
    edit,
    editRoleDep,
    getDepartmentTree,
    getRoles,
    setDefaultRole,
    setRoleMenus
  } from '../../api/role';
  import {getMenuTree} from '../../api/system';

  export default {
    data() {
      return {
        pageSize: 10,   // 每页显示多少条
        dataCount: 0,   // 总条数
        pageCurrent: 1, // 当前页
        nowData: [],
        loading: false,
        loadingTable: true,
        treeLoading: false,
        treeSubmitLoading: false,
        operationLoading: false,
        modal: false,
        editModal: false,
        treeModal: false,
        form: {
          id: '',
          name: '',
          description: '',
          is_default: 0,
        },
        editForm: {
          id: '',
          name: '',
          description: '',
          is_default: 0,
        },
        ruleValidate: {
          name: [
            {required: true, message: '角色名不能为空', trigger: 'blur'}
          ],
        },
        editRuleValidate: {
          name: [
            {required: true, message: '角色名不能为空', trigger: 'blur'}
          ],
        },
        columns: [
          {
            type: 'selection',
            width: 60,
            align: 'center'
          },
          {
            title: '角色名称',
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
            title: '描述',
            key: 'description'
          },
          {
            title: '创建时间',
            key: 'created_at'
          },
          {
            title: "设置为默认角色",
            key: "defaultRole",
            align: "center",
            width: 180,
            render: (h, params) => {
              if (params.row.is_default === 1) {
                return h("div", [
                  h(
                    "Button",
                    {
                      props: {
                        type: "success",
                        size: "small"
                      },
                      style: {
                        marginRight: "5px"
                      },
                    },
                    "默认角色"
                  )
                ]);
              } else {
                return h("div", [
                  h(
                    "Button",
                    {
                      props: {
                        type: "info",
                        size: "small"
                      },
                      style: {
                        marginRight: "5px"
                      },
                      on: {
                        click: () => {
                          this.setDefault(params.row);
                        }
                      }
                    },
                    "设为默认"
                  )
                ]);
              }
            }
          },
          {
            title: '操作',
            key: 'action',
            width: 250,
            align: 'center',
            render: (h, params) => {
              return h('div', [
                h('Button', {
                  props: {
                    type: 'warning',
                    size: 'small',
                    loading: params.row.is_loading
                  },
                  style: {
                    marginRight: '5px'
                  },
                  on: {
                    click: () => {
                      this.rowId = params.row.id;
                      this.data[params.index].is_loading = true;
                      getMenuTree(params.row.id).then((data) => {
                        this.treeData = data.result;
                        this.treeModal = true;
                        this.data[params.index].is_loading = false;
                      });
                    }
                  }
                }, '菜单权限'),
                h(
                  "Button",
                  {
                    props: {
                      type: "primary",
                      size: "small"
                    },
                    style: {
                      marginRight: "5px"
                    },
                    on: {
                      click: () => {
                        this.editDep(params.row);
                      }
                    }
                  },
                  "数据权限"
                ),
              ]);
            }
          },
          {
            title: '编辑',
            key: 'action',
            width: 100,
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
                      this.$refs.editFormValidate.resetFields();
                      this.editForm.id = params.row.id;
                      this.editForm.name = params.row.name;
                      this.editForm.description = params.row.description;
                      this.editForm.is_default = params.row.is_default;
                      this.editModal = true;
                    }
                  }
                }, '编辑')
              ]);
            }
          }
        ],
        data: [],
        treeData: [],
        rowId: '',
        dataType: 0,
        editRolePermId: "",
        modalTitle: "",
        editDepartments: [],
        depTreeLoading: true,
        submitDepLoading: false,
        depData: [],
        depModalVisible: false,
        selectCount: 0, // 多选计数
        selectList: [], // 多选数据
      }
    },
    mounted() {
      this.init();
    },
    methods: {
      init() {
        this.getRoleList();
      },
      getRoleList() {
        this.loadingTable = true;
        getRoles().then((data) => {
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
      },
      handleSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            add(this.form).then(res => {
              if (res.result === true) {
                this.loading = false;
                this.$Message.success('创建成功');
                this.$refs[name].resetFields();
                this.modal = false;
                this.getRoleList();
              } else {
                this.$Message.error('创建失败');
              }
              this.loading = false;
            });
          } else {
            this.$Message.error('发生错误！');
          }
        })
      },
      handleSubmitE(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            edit(this.editForm).then(res => {
              if (res.result) {
                this.loading = false;
                this.$Message.success('修改成功');
                this.$refs[name].resetFields();
                this.editModal = false;
                this.init();
              } else {
                this.$Message.error('修改失败');
              }
            });
          } else {
            this.$Message.error('发生错误！');
          }
        })
      },
      setDefault(v) {
        this.$Modal.confirm({
          title: "确认设置",
          loading: true,
          content: "您确认要设置 【" + v.name + "】 为注册用户默认角色?",
          onOk: () => {
            let params = {
              id: v.id,
              is_default: 1
            };
            setDefaultRole(params).then(res => {
              this.$Modal.remove();
              if (res.result === true) {
                this.$Message.success("操作成功");
                this.getRoleList();
              }
            });
          }
        });
      },
      handleReset(name) {
        this.$refs[name].resetFields();
      },
      cancel() {
        this.$refs.formValidate.resetFields();
      },
      onChange(value) {
        this.form.department_id = value;
      },
      editDep(v) {
        this.dataType = 0;
        this.editRolePermId = v.id;
        this.modalTitle = "分配 " + v.name + " 的数据权限";
        if (v.data_type) {
          this.dataType = v.data_type;
        }
        this.depTreeLoading = true;
        getDepartmentTree(this.editRolePermId).then(res => {
          if (res.result) {
            this.depData = res.result;
            this.depModalVisible = true;
            this.depTreeLoading = false;
          }
        });
      },
      submitDepEdit() {
        let depIds = "";
        if (this.dataType === 1) {
          let selectedNodes = this.$refs.depTree.getCheckedNodes();
          if (selectedNodes.length < 1) {
            this.$Message.error("请至少选择一条数据");
            return;
          }
          selectedNodes.forEach(function (e) {
            depIds += e.id + ",";
          });
          depIds = depIds.substring(0, depIds.length - 1);
        }
        this.submitDepLoading = true;
        editRoleDep({
          roleId: this.editRolePermId,
          dataType: this.dataType,
          depIds: depIds
        }).then(res => {
          this.submitDepLoading = false;
          if (res.result === true) {
            this.$Message.success("操作成功");
            this.getRoleList();
            this.depModalVisible = false;
          }
        });
      },
      treeSubmit() {
        this.treeSubmitLoading = true;
        let selectedNodes = this.$refs.tree.getCheckedAndIndeterminateNodes();
        setRoleMenus(selectedNodes, this.rowId).then((data) => {
          this.treeSubmitLoading = false;
          if (data.result) {
            this.$Message.success('设置成功');
            this.treeModal = false;
          } else {
            this.$Message.error('设置失败');
          }
        });
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
            deleteRoleData(ids).then(res => {
              this.$Modal.remove();
              if (res.result === true) {
                this.$Message.success("操作成功");
                this.init();
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
