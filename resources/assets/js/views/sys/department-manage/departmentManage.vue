<template>
  <div class="search">
    <Card>
      <Row>
        <Button @click="add" type="primary" icon="md-add">添加子部门</Button>
        <Button @click="addRoot" icon="md-add">添加一级部门</Button>
        <!-- <Button @click="delAll" icon="md-trash">批量删除</Button> -->
        <Button @click="init" icon="md-refresh">刷新</Button>
      </Row>
      <Row>
        <Col span="6">
          <Alert show-icon>
            当前选择编辑：
            <span class="select-title">{{editTitle}}</span>
            <a class="select-clear" v-if="form.id" @click="cancelEdit">取消选择</a>
          </Alert>
          <div class="tree-bar">
            <Tree
              ref="tree"
              :data="treeData"
              :load-data="loadData"
              empty-text=""
              @on-check-change="changeSelect"
              @on-select-change="selectTree"
              show-checkbox
            ></Tree>
          </div>
          <Spin size="large" fix v-if="loading"></Spin>
        </Col>
        <Col span="9">
          <Form ref="form" :model="form" :label-width="85" :rules="menuFormValidate">
            <FormItem label="上级部门" prop="parent_title">
              <Poptip trigger="click" placement="right-start" title="选择上级部门" width="250">
                <Input v-model="form.parent_title" readonly placeholder=""/>
                <div slot="content" style="position:relative;min-height:5vh">
                  <Tree :data="dataEdit" :load-data="loadData" @on-select-change="selectTreeEdit"></Tree>
                  <Spin size="large" fix v-if="loadingEdit"></Spin>
                </div>
              </Poptip>
            </FormItem>
            <FormItem label="部门名称" prop="title">
              <Input v-model="form.title" placeholder=""/>
            </FormItem>
            <FormItem label="排序值" prop="sort">
              <InputNumber :max="1000" :min="0" v-model="form.sort"></InputNumber>
              <span style="margin-left:5px">数值越小越靠前</span>
            </FormItem>
            <FormItem label="是否启用" prop="status">
              <i-switch size="large" v-model="form.status" :true-value="1" :false-value="0">
                <span slot="open">是</span>
                <span slot="close">否</span>
              </i-switch>
            </FormItem>
            <FormItem label="备注" prop="description">
              <Input v-model="form.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="输入内容"/>
            </FormItem>
            <Form-item>
              <Button
                style="margin-right:5px"
                @click="submitEdit"
                :loading="submitLoading"
                type="primary"
                icon="ios-create-outline"
              >修改并保存
              </Button>
              <Button @click="handleReset">重置</Button>
            </Form-item>
          </Form>
        </Col>
      </Row>
    
      <Modal :title="modalTitle" v-model="menuModalVisible" :mask-closable='false' :width="500">
        <Form ref="formAdd" :model="formAdd" :label-width="85" :rules="menuFormValidate">
          <div v-if="showParent">
            <FormItem label="上级部门：">
              {{form.title}}
            </FormItem>
          </div>
          <FormItem label="部门名称" prop="title">
            <Input v-model="formAdd.title" placeholder=""/>
          </FormItem>
          <FormItem label="排序值" prop="sort">
            <InputNumber :max="1000" :min="0" v-model="formAdd.sort"></InputNumber>
            <span style="margin-left:5px">数值越小越靠前</span>
          </FormItem>
          <FormItem label="是否启用" prop="status">
            <i-switch size="large" v-model="formAdd.status" :true-value="1" :false-value="0">
              <span slot="open">是</span>
              <span slot="close">否</span>
            </i-switch>
          </FormItem>
          <FormItem label="备注" prop="description">
            <Input v-model="formAdd.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                   placeholder="输入内容"></Input>
          </FormItem>
        </Form>
        <div slot="footer">
          <Button type="text" @click="cancelAdd">取消</Button>
          <Button type="primary" :loading="submitLoading" @click="submitAdd">提交</Button>
        </div>
      </Modal>
    </Card>
  </div>
</template>

<script>
  import {initDepartment, loadDepartment, addDepartment, editDepartment} from "api/system";
  import "./departmentManage.css";

  export default {
    name: "departmentManage",
    data() {
      return {
        loading: false,
        loadingEdit: false,
        treeData: [],
        editTitle: "",
        data: [],
        dataEdit: [],
        selectList: [],
        selectCount: 0,
        showParent: false,
        menuModalVisible: false,
        modalTitle: "",
        formAdd: {},
        form: {
          id: "",
          parent_id: "",
          parent_title: "",
          sort: null,
          description: "",
          status: 1
        },
        menuFormValidate: {
          title: [{required: true, message: "名称不能为空", trigger: "blur"}]
        },
        submitLoading: false
      };
    },
    methods: {
      init() {
        this.getParentList();
        this.getParentListEdit();
      },
      getParentList() {
        this.loading = true;
        initDepartment().then(data => {
          data.result.forEach(function (e) {
            if (e.is_parent) {
              e.loading = false;
              e.children = [];
            }
          });
          this.treeData = data.result;
          this.loading = false;
        });
      },
      getParentListEdit() {
        this.loadingEdit = true;
        initDepartment().then(res => {
          this.loadingEdit = false;
          res.result.forEach(function (e) {
            if (e.is_parent) {
              e.loading = false;
              e.children = [];
            }
          });
          // 头部加入一级
          let first = {
            id: "0",
            title: "一级部门"
          };
          res.result.unshift(first);
          this.dataEdit = res.result;
        });
      },
      loadData(item, callback) {
        loadDepartment(item.id).then(res => {
          res.result.forEach(function (e) {
            if (e.is_parent) {
              e.loading = false;
              e.children = [];
            }
          });
          callback(res.result);
        });
      },
      selectTree(v) {
        if (v.length <= 0) {
          this.cancelEdit();
        } else {
          this.editStatus = Number(v[0].status) === 0;
          // 转换null为""
          for (let attr in v[0]) {
            if (v[0][attr] === null) {
              v[0][attr] = "";
            }
          }
          let str = JSON.stringify(v[0]);
          let data = JSON.parse(str);
          this.form = data;
          this.editTitle = data.title;
        }
      },
      cancelEdit() {
        let data = this.$refs.tree.getSelectedNodes()[0];
        if (data) {
          data.selected = false;
        }
        this.isMenu = false;
        this.isButton = false;
        this.$refs.form.resetFields();
        delete this.form.id;
        this.editTitle = "";
      },
      changeSelect(v) {
        this.selectCount = v.length;
        this.selectList = v;
      },
      selectTreeEdit(v) {
        if (v.length > 0) {
          // 转换null为""
          for (let attr in v[0]) {
            if (v[0][attr] === null) {
              v[0][attr] = "";
            }
          }
          let str = JSON.stringify(v[0]);
          let data = JSON.parse(str);
          this.form.parent_id = data.id;
          this.form.parent_title = data.title;
        }
      },
      add() {
        if (this.form.id == "" || this.form.id == null) {
          this.$Message.warning("请先点击选择一个部门");
          return;
        }
        this.modalTitle = "添加子部门";
        this.showParent = true;
        this.formAdd = {
          parent_id: this.form.id,
          sort: 1,
          status: 1
        };
        this.menuModalVisible = true;
      },
      addRoot() {
        this.modalTitle = "添加一级部门";
        this.showParent = false;
        this.formAdd = {
          parent_id: 0,
          sort: 1,
          status: 1
        };
        this.menuModalVisible = true;
      },
      cancelAdd() {
        this.menuModalVisible = false;
      },
      handleReset() {
        this.$refs.form.resetFields();
        this.editStatus = true;
        this.form.status = 0;
      },
      submitEdit() {
        this.$refs.form.validate(valid => {
          if (valid) {
            if (!this.form.id) {
              this.$Message.warning("请先点击选择要修改的部门");
              return;
            }
            this.submitLoading = true;
            editDepartment(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("编辑成功");
                this.init();
                this.menuModalVisible = false;
              }
            });
          }
        });
      },
      submitAdd() {
        this.$refs.formAdd.validate(valid => {
          if (valid) {
            this.submitLoading = true;
            addDepartment(this.formAdd).then(res => {
              this.submitLoading = false;
              if (res.result === true) {
                this.$Message.success("添加成功");
                this.init();
                this.menuModalVisible = false;
              }
            });
          }
        });
      },
    },
    mounted() {
      this.init();
    }
  };
</script>

<style scoped>
</style>