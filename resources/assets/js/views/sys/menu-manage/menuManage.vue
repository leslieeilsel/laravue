<template>
  <div class="search">
    <Card>
      <Row class="operation">
        <Button @click="addChildMenu" type="primary" icon="md-add">添加子节点</Button>
        <Button @click="addRootMenu" icon="md-add">添加一级菜单</Button>
        <Button @click="delAll" icon="md-trash">批量删除</Button>
        <!--<Dropdown @on-click="handleDropdown">-->
        <!--<Button>更多操作-->
        <!--<Icon type="md-arrow-dropdown"></Icon>-->
        <!--</Button>-->
        <!--<DropdownMenu slot="list">-->
        <!--<DropdownItem name="refresh">刷新</DropdownItem>-->
        <!--<DropdownItem name="expandOne">仅显示一级</DropdownItem>-->
        <!--<DropdownItem name="expandTwo">仅展开两级</DropdownItem>-->
        <!--<DropdownItem name="expandAll">展开所有</DropdownItem>-->
        <!--</DropdownMenu>-->
        <!--</Dropdown>-->
      </Row>
      <Row>
        <Col span="6">
          <Alert show-icon>
            当前选择编辑：
            <span class="select-title">{{editTitle}}</span>
            <a class="select-clear" v-if="menuForm.id" @click="cancelEdit">取消选择</a>
          </Alert>
          <div class="tree-bar">
            <Tree
              ref="tree"
              :data="data"
              show-checkbox
              @on-check-change="changeSelect"
              @on-select-change="selectTree"
            ></Tree>
          </div>
          <Spin size="large" fix v-if="loading"></Spin>
        </Col>
        <Col span="9">
          <Form ref="menuForm" :model="menuForm" :label-width="100" :rules="menuFormValidate">
            <FormItem label="名称" prop="title">
              <Input v-model="menuForm.title" placeholder=""/>
            </FormItem>
            <FormItem label="英文名" prop="name">
              <Input v-model="menuForm.name" placeholder=""/>
            </FormItem>
            <FormItem label="路径" prop="path">
              <Input v-model="menuForm.path" placeholder=""/>
            </FormItem>
            <FormItem label="图标" prop="icon">
              <Input :icon="menuForm.icon" v-model="menuForm.icon" @on-focus="showEditIcon(0)" placeholder=""/>
            </FormItem>
            <FormItem label="链接类型" prop="link_type">
              <RadioGroup v-model="menuForm.link_type" @on-change="changeLinkType">
                <Radio :label="0">
                  <Icon type="md-compass" size="16" style="margin-bottom:3px;"></Icon>
                  <span>本地链接</span>
                </Radio>
                <Radio :label="1">
                  <Icon type="md-link" size="16" style="margin-bottom:3px;"></Icon>
                  <span>第三方链接</span>
                </Radio>
              </RadioGroup>
            </FormItem>
            <FormItem label="前端组件" prop="component">
              <Input v-model="menuForm.component" :disabled="menuFormComponent" placeholder=""/>
            </FormItem>
            <FormItem label="链接地址" prop="url" v-if="menuForm.link_type===1">
              <Poptip
                trigger="focus"
                placement="right"
                width="320"
                word-wrap
                title="提示"
                content="链接开头必须为 http:// 或 https://"
              >
                <Input v-model="menuForm.url" placeholder=""/>
              </Poptip>
            </FormItem>
            <FormItem label="排序值" prop="sort">
              <InputNumber :max="1000" :min="0" v-model="menuForm.sort"></InputNumber>
              <span style="margin-left:5px">值越小越靠前</span>
            </FormItem>
            <FormItem label="是否启用" prop="status">
              <i-switch size="large" v-model="menuForm.enabled" :true-value="1" :false-value="0">
                <span slot="open">启用</span>
                <span slot="close">禁用</span>
              </i-switch>
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
    </Card>
    
    <Modal
      :title="modalTitle"
      v-model="menuModalVisible"
      :mask-closable="false"
      :width="500"
      :styles="{top: '100px'}"
    >
      <Form ref="menuFormAdd" :model="menuFormAdd" :label-width="85" :rules="menuFormValidate">
        <div v-if="showParent">
          <FormItem label="上级节点：">{{parentTitle}}</FormItem>
        </div>
        <FormItem label="名称" prop="title">
          <Input v-model="menuFormAdd.title" placeholder=""/>
        </FormItem>
        <FormItem label="英文名" prop="name">
          <Input v-model="menuFormAdd.name" placeholder=""/>
        </FormItem>
        <FormItem label="路径" prop="path">
          <Input v-model="menuFormAdd.path" placeholder=""/>
        </FormItem>
        <FormItem label="图标" prop="icon">
          <Input :icon="menuFormAdd.icon" v-model="menuFormAdd.icon" @on-focus="showEditIcon(1)" placeholder=""/>
        </FormItem>
        <FormItem label="链接类型" prop="link_type">
          <RadioGroup v-model="menuFormAdd.link_type" @on-change="changeAddLinkType">
            <Radio :label="0">
              <Icon type="md-compass" size="16" style="margin-bottom:3px;"></Icon>
              <span>本地链接</span>
            </Radio>
            <Radio :label="1">
              <Icon type="md-link" size="16" style="margin-bottom:3px;"></Icon>
              <span>第三方链接</span>
            </Radio>
          </RadioGroup>
        </FormItem>
        <FormItem label="前端组件" prop="component">
          <Input v-model="menuFormAdd.component" :disabled="menuFormAddComponent" placeholder=""/>
        </FormItem>
        <FormItem label="链接地址" prop="url" v-if="menuFormAdd.link_type===1">
          <Poptip
            trigger="focus"
            placement="right"
            width="320"
            word-wrap
            title="提示"
            content="链接开头必须为 http:// 或 https://"
          >
            <Input v-model="menuFormAdd.url" placeholder=""/>
          </Poptip>
        </FormItem>
        <FormItem label="排序值" prop="sort">
          <InputNumber :max="1000" :min="0" v-model="menuFormAdd.sort"></InputNumber>
          <span style="margin-left:5px">值越小越靠前</span>
        </FormItem>
        <FormItem label="是否启用" prop="enabled">
          <i-switch size="large" v-model="menuFormAdd.enabled" :true-value="1" :false-value="0">
            <span slot="open">启用</span>
            <span slot="close">禁用</span>
          </i-switch>
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="text" @click="cancelAdd">取消</Button>
        <Button type="primary" :loading="submitLoading" @click="submitAdd">提交</Button>
      </div>
    </Modal>
    <Modal title="选择图标" v-model="iconModalVisible" :width="800" :styles="{top: '100px'}" footer-hide>
      <icon-choose @on-select="handleSelectIcon"></icon-choose>
    </Modal>
  </div>
</template>
<script>
  import {getAllMenuList, getmenus, addMenu, editMenu, deleteMenu} from '../../../api/system';
  import IconChoose from "../../my-components/icon-choose";

  export default {
    components: {
      IconChoose
    },
    data() {
      return {
        loading: false,
        button1: '北京',
        editTitle: '',
        modalTitle: '',
        menuFormComponent: false,
        menuFormAddComponent: false,
        parentTitle: "",
        expandLevel: 1,
        selectList: [],
        selectCount: 0,
        submitLoading: false,
        iconType: 0,
        iconModalVisible: false,
        menuModalVisible: false,
        showParent: false,
        menuForm: {
          link_type: 0,
          id: "",
          parent_id: "",
          sort: 0,
          level: null,
          enabled: 1,
          path: "",
          url: ''
        },
        menuFormAdd: {},
        beforeValue: '',
        data: [],
        menuFormValidate: {
          title: [{required: true, message: "名称不能为空", trigger: "blur"}],
          name: [{required: true, message: "英文名不能为空", trigger: "blur"}],
          path: [{required: true, message: "路径不能为空", trigger: "blur"}],
        },
      }
    },
    methods: {
      init() {
        this.getAllList();
      },
      getAllList() {
        this.loading = true;
        getmenus().then(res => {
          this.loading = false;
          if (res.result) {
            // 仅展开指定级数 默认一级
            // let expandLevel = this.expandLevel;
            // res.result.forEach(function (e) {
            //   if (expandLevel === 1) {
            //     if (e.level === 1) {
            //       e.expand = false;
            //     }
            //     if (e.children && e.children.length > 0) {
            //       e.children.forEach(function (c) {
            //         if (c.level === 2) {
            //           c.expand = false;
            //         }
            //       });
            //     }
            //   } else {
            //     if (e.children && e.children.length > 0) {
            //       e.children.forEach(function (c) {
            //         if (expandLevel === 2) {
            //           if (c.level === 2) {
            //             c.expand = false;
            //           }
            //         }
            //       });
            //     }
            //   }
            // });
            this.data = res.result;
          }
        });
      },
      changeSelect(v) {
        this.selectCount = v.length;
        this.selectList = v;
      },
      handleReset() {
        this.$refs.menuForm.resetFields();
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
          let menu = JSON.parse(str);
          this.menuForm = menu;
          this.beforeValue = this.menuForm.component;
          this.menuFormComponent = this.menuForm.link_type !== 0;
          this.editTitle = menu.title;
        } else {
          this.cancelEdit();
        }
      },
      cancelEdit() {
        let data = this.$refs.tree.getSelectedNodes()[0];
        if (data) {
          data.selected = false;
        }
        this.$refs.menuForm.resetFields();
        delete this.menuForm.id;
        this.editTitle = "";
      },
      submitEdit() {
        this.$refs.menuForm.validate(valid => {
          if (valid) {
            if (!this.menuForm.id) {
              this.$Message.warning("请先点击选择要修改的菜单节点");
              return;
            }
            this.submitLoading = true;
            editMenu(this.menuForm).then(res => {
              this.submitLoading = false;
              if (res.result === true) {
                this.$Message.success("编辑成功");
                // util.initRouter(this);
                this.init();
                this.menuModalVisible = false;
              }
            });
          }
        });
      },
      showEditIcon(v) {
        this.iconType = v;
        this.iconModalVisible = true;
      },
      handleSelectIcon(v) {
        if (this.iconType === 0) {
          this.menuForm.icon = v;
        } else {
          this.menuFormAdd.icon = v;
        }
        this.iconModalVisible = false;
      },
      handleDropdown(name) {
        if (name === "expandOne") {
          this.expandLevel = 1;
          this.getAllList();
        } else if (name === "expandTwo") {
          this.expandLevel = 2;
          this.getAllList();
        } else if (name === "expandAll") {
          this.expandLevel = 3;
          this.getAllList();
        } else if (name === "refresh") {
          this.getAllList();
        }
      },
      addChildMenu() {
        if (this.menuForm.id === "" || this.menuForm.id == null) {
          this.$Message.warning("请先点击选择一个菜单权限节点");
          return;
        }
        this.parentTitle = this.menuForm.title;
        this.modalTitle = "添加菜单";
        this.showParent = true;
        this.menuFormAdd = {
          parent_id: this.menuForm.id,
          sort: 0,
          enabled: 1,
          link_type: 0,
        };
        this.menuModalVisible = true;
      },
      addRootMenu() {
        this.modalTitle = "添加一级菜单";
        this.showParent = false;
        this.menuFormAdd = {
          parent_id: 0,
          sort: 0,
          enabled: 1
        };
        this.menuModalVisible = true;
      },
      cancelAdd() {
        this.menuModalVisible = false;
      },
      submitAdd() {
        this.$refs.menuFormAdd.validate(valid => {
          if (valid) {
            this.submitLoading = true;
            addMenu(this.menuFormAdd).then(res => {
              this.submitLoading = false;
              if (res.result === true) {
                this.$Message.success("添加成功");
                // util.initRouter(this);
                this.init();
                this.menuModalVisible = false;
              }
            });
          }
        });
      },
      delAll() {
        if (this.selectCount <= 0) {
          this.$Message.warning("您还未勾选要删除的数据");
          return;
        }
        this.$Modal.confirm({
          title: "确认删除",
          loading: true,
          content: "您确认要删除所选的 " + this.selectCount + " 条数据?",
          onOk: () => {
            let ids = "";
            this.selectList.forEach(function(e) {
              ids += e.id + ",";
            });
            ids = ids.substring(0, ids.length - 1);
            deleteMenu(ids).then(res => {
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
      },
      changeLinkType(v) {
        if (v !== 1) {
          this.menuFormComponent = false;
          this.menuForm.component = this.beforeValue;
        } else {
          this.menuFormComponent = true;
          this.menuForm.component = 'views/sys/monitor/monitor';
        }
      },
      changeAddLinkType(v) {
        if (v !== 1) {
          this.menuFormAddComponent = false;
          this.menuFormAdd.component = '';
        } else {
          this.menuFormAddComponent = true;
          this.menuFormAdd.component = 'views/sys/monitor/monitor';
        }
      },
    },
    mounted() {
      this.init();
    }
  }
</script>
