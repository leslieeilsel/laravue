<template>
  <div class="search">
    <Card>
      <Row class="operation">
        <Button @click="add" type="primary" icon="md-add">添加子项目</Button>
        <Button @click="addRoot" icon="md-add">添加一级项目</Button>
        <Button @click="delAll" icon="md-trash">批量删除</Button>
        <Button @click="getParentList" icon="md-refresh">刷新</Button>
      </Row>
      <Row type="flex" justify="start" class="code-row-bg">
        <Col span="6">
          <Alert show-icon>
            当前选择编辑：
            <span class="select-title">{{editTitle}}</span>
            <a class="select-clear" v-if="form.id" @click="cancelEdit">取消选择</a>
          </Alert>
<!--          <Input-->
<!--            v-model="searchKey"-->
<!--            suffix="ios-search"-->
<!--            @on-change="search"-->
<!--            placeholder="输入节点名搜索"-->
<!--            clearable-->
<!--          />-->
          <div class="tree-bar">
            <Tree
              ref="tree"
              :data="data"
              :load-data="loadData"
              show-checkbox
              @on-check-change="changeSelect"
              @on-select-change="selectTree"
            ></Tree>
          </div>
          <Spin size="large" fix v-if="loading"></Spin>
        </Col>
        <Col span="9">
          <Form ref="form" :model="form" :label-width="85" :rules="formValidate">
            <FormItem label="上级项目" prop="parent_title">
              <Poptip trigger="click" placement="right-start" title="选择上级项目" width="250">
                <Input v-model="form.parent_title" readonly placeholder=""/>
                <div slot="content" style="position:relative;min-height:5vh">
                  <Tree :data="dataEdit" :load-data="loadData" @on-select-change="selectTreeEdit"></Tree>
                  <Spin size="large" fix v-if="loadingEdit"></Spin>
                </div>
              </Poptip>
            </FormItem>
            <FormItem label="项目名称" prop="title">
              <Input v-model="form.title" placeholder=""/>
            </FormItem>
            <div v-if="editSon">
              <FormItem label="业主" prop="owner">
                <Input v-model="form.owner" placeholder=""/>
              </FormItem>
              <FormItem label="建设单位" prop="unit">
                <Input v-model="form.unit" placeholder=""/>
              </FormItem>
              <FormItem label="项目金额" prop="amount">
                <Input v-model="form.amount" placeholder="支持小数点后两位"/>
              </FormItem>
            </div>
            <FormItem label="计划时间">
              <Row>
                <Col span="11">
                  <DatePicker type="date" placeholder="开始时间" format="yyyy-MM-dd" v-model="form.plan_start_at"></DatePicker>
                </Col>
                <Col span="2" style="text-align: center">-</Col>
                <Col span="11">
                  <DatePicker type="date" placeholder="结束时间" format="yyyy-MM-dd" v-model="form.plan_end_at"></DatePicker>
                </Col>
              </Row>
            </FormItem>
            <FormItem label="实际时间">
              <Row>
                <Col span="11">
                  <DatePicker type="date" placeholder="开始时间" format="yyyy-MM-dd" v-model="form.actual_start_at"></DatePicker>
                </Col>
                <Col span="2" style="text-align: center">-</Col>
                <Col span="11">
                  <DatePicker type="date" placeholder="结束时间" format="yyyy-MM-dd" v-model="form.actual_end_at"></DatePicker>
                </Col>
              </Row>
            </FormItem>
            <FormItem label="所在经度" prop="lng">
              <Input v-model="form.lng" placeholder=""/>
            </FormItem>
            <FormItem label="所在纬度" prop="lat">
              <Input v-model="form.lat" placeholder=""/>
            </FormItem>
            <Form-item>
              <Button
                @click="submitEdit"
                :loading="submitLoading"
                type="primary"
                icon="ios-create-outline"
                style="margin-right:5px"
              >修改并保存
              </Button>
              <Button @click="handleReset">重置</Button>
            </Form-item>
          </Form>
        </Col>
      </Row>
    </Card>
    
    <Modal :title="modalTitle" v-model="modalVisible" :mask-closable="false" :width="500">
      <Form ref="formAdd" :model="formAdd" :label-width="85" :rules="formValidate">
        <div v-if="showParent">
          <FormItem label="上级项目：">{{form.title}}</FormItem>
        </div>
        <FormItem label="项目名称" prop="title">
          <Input v-model="formAdd.title" placeholder=""/>
        </FormItem>
        <div v-if="addSon">
          <FormItem label="业主" prop="owner">
            <Input v-model="formAdd.owner" placeholder=""/>
          </FormItem>
          <FormItem label="建设单位" prop="unit">
            <Input v-model="formAdd.unit" placeholder=""/>
          </FormItem>
          <FormItem label="项目金额" prop="amount">
            <Input v-model="formAdd.amount" placeholder="支持小数点后两位"/>
          </FormItem>
        </div>
        <FormItem label="计划时间">
          <Row>
            <Col span="11">
              <DatePicker type="date" placeholder="开始时间" format="yyyy-MM-dd" v-model="formAdd.plan_start_at"></DatePicker>
            </Col>
            <Col span="2" style="text-align: center">-</Col>
            <Col span="11">
              <DatePicker type="date" placeholder="结束时间" format="yyyy-MM-dd" v-model="formAdd.plan_end_at"></DatePicker>
            </Col>
          </Row>
        </FormItem>
        <FormItem label="实际时间">
          <Row>
            <Col span="11">
              <DatePicker type="date" placeholder="开始时间" format="yyyy-MM-dd" v-model="formAdd.actual_start_at"></DatePicker>
            </Col>
            <Col span="2" style="text-align: center">-</Col>
            <Col span="11">
              <DatePicker type="date" placeholder="结束时间" format="yyyy-MM-dd" v-model="formAdd.actual_end_at"></DatePicker>
            </Col>
          </Row>
        </FormItem>
        <FormItem label="所在经度" prop="lng">
          <Input v-model="formAdd.lng" placeholder=""/>
        </FormItem>
        <FormItem label="所在纬度" prop="lat">
          <Input v-model="formAdd.lat" placeholder=""/>
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="text" @click="modalVisible=false">取消</Button>
        <Button type="primary" :loading="submitLoading" @click="submitAdd">提交</Button>
      </div>
    </Modal>
  </div>
</template>

<script>
  import "./projectInfo.css";
  import {initProjectInfo, loadDepartment, addDepartment, editDepartment, deleteProject} from "../../../api/project";
  
  export default {
    name: "tree",
    data() {
      return {
        loading: false, // 树加载状态
        loadingEdit: false, // 编辑上级树加载状态
        modalVisible: false, // 添加显示
        selectList: [], // 多选数据
        selectCount: 0, // 多选计数
        showParent: false, // 显示上级标识
        addSon: true,
        editSon: true,
        modalTitle: "", // 添加标题
        editTitle: "", // 编辑节点名称
        searchKey: "", // 搜索树
        form: {
          id: "",
          parent_id: "",
          parent_title: "",
          owner: '',
          unit: '',
          amount: '',
          plan_start_at: '',
          plan_end_at: '',
          actual_start_at: '',
          actual_end_at: '',
          lng: '',
          lat: ''
        },
        formAdd: {
          // 添加对象初始化数据
        },
        formValidate: {
          // 表单验证规则
          title: [{required: true, message: "名称不能为空", trigger: "blur"}]
        },
        submitLoading: false,
        data: [],
        dataEdit: []
      };
    },
    methods: {
      init() {
        // 初始化一级节点
        this.getParentList();
        // 初始化一级节点为编辑上级节点使用
        this.getParentListEdit();
      },
      getParentList() {
        this.loading = true;
        initProjectInfo().then(res => {
          this.loading = false;
          if (res.result) {
            res.result.forEach(function(e) {
              if (e.is_parent) {
                e.loading = false;
                e.children = [];
              }
            });
            this.data = res.result;
          }
        });
      },
      getParentListEdit() {
        this.loadingEdit = true;
        initProjectInfo().then(res => {
          this.loadingEdit = false;
          if (res.result) {
            res.result.forEach(function(e) {
              if (e.is_parent) {
                e.loading = false;
                e.children = [];
              }
            });
            // 头部加入一级
            let first = {
              id: "0",
              title: "一级节点"
            };
            res.result.unshift(first);
            this.dataEdit = res.result;
          }
        });
      },
      loadData(item, callback) {
        // 异步加载树子节点数据
        loadDepartment(item.id).then(res => {
          if (res.result) {
            res.result.forEach(function(e) {
              if (e.is_parent) {
                e.loading = false;
                e.children = [];
              }
            });
            callback(res.result);
          }
        });
      },
      search() {
        // 搜索树
        if (this.searchKey) {
          // 模拟请求
          // this.loading = true;
          // this.getRequest("搜索请求路径", { title: this.searchKey }).then(res => {
          //   this.loading = false;
          //   if (res.success === true) {
          //     this.data = res.result;
          //   }
          // });
          // 模拟请求成功
          this.data = [
            {
              title: "这里需要请求后台接口",
              id: "1",
              parentId: "0",
              parent_title: "一级节点",
              status: 0
            },
            {
              title: "所以这里是假数据",
              id: "4",
              parentId: "0",
              parent_title: "一级节点",
              status: 0
            },
            {
              title: "我是被禁用的节点",
              id: "5",
              parentId: "0",
              parent_title: "一级节点",
              status: -1
            }
          ];
        } else {
          // 为空重新加载
          this.getParentList();
        }
      },
      selectTree(v) {
        if (v.length > 0) {
          // 转换null为""
          for (let attr in v[0]) {
            if (v[0][attr] === null) {
              v[0][attr] = "";
            }
          }
          this.editSon = true;
          let str = JSON.stringify(v[0]);
          let data = JSON.parse(str);
          this.form = data;
          if (this.form.parent_id !== 0) {
            this.editSon = false;
          }
          this.editTitle = data.title;
        } else {
          this.cancelEdit();
        }
      },
      cancelEdit() {
        let data = this.$refs.tree.getSelectedNodes()[0];
        if (data) {
          data.selected = false;
        }
        this.$refs.form.resetFields();
        delete this.form.id;
        this.editTitle = "";
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
      handleReset() {
        this.$refs.form.resetFields();
        this.form.status = 0;
      },
      submitEdit() {
        this.$refs.form.validate(valid => {
          if (valid) {
            if (!this.form.id) {
              this.$Message.warning("请先点击选择要修改的节点");
              return;
            }
            this.submitLoading = true;
            editDepartment(this.form).then(res => {
              this.submitLoading = false;
              if (res.result) {
                this.$Message.success("编辑成功");
                this.init();
                this.modalVisible = false;
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
                this.modalVisible = false;
              }
            });
          }
        });
      },
      add() {
        if (this.form.id == "" || this.form.id == null) {
          this.$Message.warning("请先点击选择一个节点");
          return;
        }
        this.modalTitle = "添加子节点";
        this.showParent = true;
        this.addSon = false;
        this.formAdd = {
          parent_id: this.form.id,
        };
        this.modalVisible = true;
      },
      addRoot() {
        this.modalTitle = "添加一级项目";
        this.showParent = false;
        this.formAdd = {
          title: '',
          owner: '',
          unit: '',
          amount: '',
          parent_id: 0,
          plan_start_at: '',
          plan_end_at: '',
          actual_start_at: '',
          actual_end_at: '',
          lng: '',
          lat: ''
        };
        this.modalVisible = true;
      },
      changeSelect(v) {
        this.selectCount = v.length;
        this.selectList = v;
      },
      delAll() {
        if (this.selectCount <= 0) {
          this.$Message.warning("您还未勾选要删除的数据");
          return;
        }
        this.$Modal.confirm({
          title: "确认删除",
          content: "您确认要删除所选的 " + this.selectCount + " 条数据?",
          onOk: () => {
            let ids = "";
            this.selectList.forEach(function (e) {
              ids += e.id + ",";
            });
            ids = ids.substring(0, ids.length - 1);
            deleteProject(ids).then(res => {
              this.$Modal.remove();
              if (res.result === true) {
                this.$Message.success("删除成功");
                // util.initRouter(this);
                this.selectList = [];
                this.selectCount = 0;
                this.cancelEdit();
                this.init();
              }
            });
          }
        });
      }
    },
    mounted() {
      this.init();
    }
  };
</script>