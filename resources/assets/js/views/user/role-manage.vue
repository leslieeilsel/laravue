<template>
  <div>
    <Card>
      <p class="btnGroup">
        <Button type="primary" @click="modal = true" icon="md-add">添加角色</Button>
        <Button type="error" disabled icon="md-trash">删除</Button>
        <!-- 菜单权限 -->
        <Modal
          v-model="modal"
          @on-cancel="cancel"
          title="添加角色">
          <Form ref="formValidate" :model="form" :rules="ruleValidate" :label-width="130">
            <FormItem label="角色名称" prop="name">
              <Input v-model="form.name" placeholder="必填项"></Input>
            </FormItem>
            <FormItem label="是否设置为默认角色" prop="is_default">
              <i-switch v-model="form.is_default" size="large" :true-value="1" :false-value="0">
                <span slot="open">是</span>
                <span slot="close">否</span>
              </i-switch>
            </FormItem>
            <FormItem label="备注" prop="description">
              <Input type="textarea" v-model="form.description" placeholder="可选项"></Input>
            </FormItem>
          </Form>
          <div slot="footer">
            <Button @click="handleReset('formValidate')" style="margin-left: 8px">重置</Button>
            <Button type="primary" @click="handleSubmit('formValidate')" :loading="loading">提交</Button>
          </div>
        </Modal>
        <Modal
          v-model="treeModal"
          :width="500"
          title="设置菜单权限">
          <Tree ref="tree" :data="treeData" show-checkbox multiple></Tree>
          <div slot="footer">
            <Button type="primary" @click="treeSubmit()" :loading="treeSubmitLoading">提交</Button>
          </div>
        </Modal>
        <!-- 数据权限 -->
        <Modal :title="modalTitle" v-model="depModalVisible" :mask-closable='false' :width="500" :styles="{top: '30px'}"
               class="depModal">
          <Form :label-width="65">
            <FormItem label="数据范围">
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
          <div v-if="dataType===1" style="margin-top:15px">
            <Tree ref="depTree" :data="depData" show-checkbox style="margin-top:15px"></Tree>
            <Spin size="large" v-if="depTreeLoading"></Spin>
          </div>
          <div slot="footer">
            <Button type="text" @click="depModalVisible=false">取消</Button>
            <Button type="primary" :loading="submitDepLoading" @click="submitDepEdit">提交</Button>
          </div>
        </Modal>
      </p>
      <Table border :columns="columns" :data="data" :loading="loadingTable"></Table>
    </Card>
  </div>
</template>
<script>
  import './users.css';
  import {add, getRoles, setRoleMenus, setDefaultRole, getDepartmentTree, editRoleDep} from '../../api/role';
  import {getMenuTree} from '../../api/system';

  export default {
    data() {
      return {
        loading: false,
        loadingTable: true,
        treeLoading: false,
        treeSubmitLoading: false,
        operationLoading: false,
        modal: false,
        treeModal: false,
        form: {
          name: '',
          description: '',
          is_default: 0,
        },
        ruleValidate: {
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
              if (params.row.is_default) {
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
          this.loadingTable = false;
        });
      },
      handleSubmit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            add(this.form).then(res => {
              if (res.result) {
                this.loading = false;
                this.$Message.success('创建成功');
                this.$refs[name].resetFields();
                this.modal = false;
                this.loadingTable = true;
                getRoles().then((data) => {
                  this.data = data.result;
                  this.loadingTable = false;
                });
              } else {
                this.$Message.error('创建失败');
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
      }
    }
  }
</script>
