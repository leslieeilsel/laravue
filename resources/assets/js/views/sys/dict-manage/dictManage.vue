<template>
    <div>
        <Card>
            <Row type="flex" justify="space-between" class="code-row-bg">
                <Col v-if="expand" span="5">
                    <Row class="operation">
                        <Button @click="addDict" type="primary" icon="md-add">添加字典</Button>
                        <Dropdown @on-click="handleDropdown" trigger="click">
                            <Button>
                                更多操作
                                <Icon type="md-arrow-dropdown"/>
                            </Button>
                            <DropdownMenu slot="list">
                                <DropdownItem name="editDict">编辑字典</DropdownItem>
                                <DropdownItem name="delDict">删除字典</DropdownItem>
                                <DropdownItem name="refreshDict">刷新</DropdownItem>
                            </DropdownMenu>
                        </Dropdown>
                    </Row>
                    <Alert show-icon>
                        当前选择： <span class="select-title">{{editTitle}}</span>
                        <a class="select-clear" v-if="editTitle" @click="cancelEdit">取消选择</a>
                    </Alert>
                    <div class="tree-bar">
                        <Tree ref="tree" :data="treeData" @on-select-change="selectTree"></Tree>
                    </div>
                    <Spin size="large" fix v-if="treeLoading"></Spin>
                </Col>
                <div class="expand">
                    <Icon :type="expandIcon" size="16" class="icon" @click="changeExpand"/>
                </div>
                <Col :span="span">
                    <Row class="operation">
                        <Button @click="add" type="primary" icon="md-add">添加数据</Button>
                        <Button @click="delAll" icon="md-trash">批量删除</Button>
                        <Button @click="getDataList" icon="md-refresh">刷新数据</Button>
                        <circleLoading v-if="operationLoading"/>
                    </Row>
                    <Row>
                        <Alert show-icon>
                            已选择 <span class="select-count">{{selectCount}}</span> 项
                            <a class="select-clear" @click="clearSelectAll">清空</a>
                        </Alert>
                    </Row>
                    <Row>
                        <Table :loading="loading" border :columns="columns" :data="data" sortable="custom" @on-sort-change="changeSort" @on-selection-change="showSelect" ref="table"></Table>
                    </Row>
                </Col>
            </Row>
        </Card>

        <Modal
            :title="dictModalTitle"
            v-model="dictModalVisible"
            :mask-closable='false'
            :width="500"
            :styles="{top: '100px'}">
            <Form ref="dictForm" :model="dictForm" :label-width="70" :rules="dictFormValidate">
                <FormItem label="字典名称" prop="title">
                    <Input v-model="dictForm.title" placeholder=""/>
                </FormItem>
                <FormItem label="字典类型" prop="type">
                    <!--<Tooltip placement="right" content="建议使用唯一的英文名，非开发人员请谨填写" class="form-tooltip">-->
                    <Input v-model="dictForm.type" placeholder="建议使用唯一的英文名称，非开发人员请谨填写"/>
                    <!--</Tooltip>-->
                </FormItem>
                <FormItem label="备注" prop="description">
                    <Input v-model="dictForm.description" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="输入内容"/>
                </FormItem>
            </Form>
            <div slot="footer">
                <Button type="text" @click="dictModalVisible=false">取消</Button>
                <Button type="primary" :loading="submitLoading" @click="handelSubmitDict">提交</Button>
            </div>
        </Modal>
        <Modal :title="modalTitle" v-model="modalVisible" :mask-closable='false' :width="500" :styles="{top: '30px'}">
            <Form ref="form" :model="form" :label-width="70" :rules="formValidate">
                <FormItem label="名称" prop="title">
                    <Input v-model="form.title" placeholder=""/>
                </FormItem>
                <FormItem label="数据值" prop="value">
                    <Input v-model="form.value" placeholder=""/>
                </FormItem>
                <FormItem label="备注" prop="description">
                    <Input v-model="form.description" placeholder=""/>
                </FormItem>
                <FormItem label="排序值" prop="sort">
                    <InputNumber :max="1000" :min="0" v-model="form.sort"></InputNumber>
                    <span style="margin-left:5px">数值越小越靠前</span>
                </FormItem>
                <FormItem label="是否启用" prop="status">
                    <i-switch size="large" v-model="form.status" :true-value="1" :false-value="0">
                        <span slot="open">启用</span>
                        <span slot="close">禁用</span>
                    </i-switch>
                </FormItem>
            </Form>
            <div slot="footer">
                <Button type="text" @click="modalVisible=false">取消</Button>
                <Button type="primary" :loading="submitLoading" @click="handelSubmit">提交</Button>
            </div>
        </Modal>
    </div>
</template>

<script>
    import {
        getAllDictList,
        addDict,
        editDict,
        deleteDict,
        getAllDictDataList,
        addDictData,
        editDictData,
        deleteDictData} from 'api/system';
    import "./dictManage.css";

    export default {
        name: "dictManage",
        data() {
            return {
                treeData: [],
                loading: false,
                treeLoading: false,
                submitLoading: false,
                operationLoading: false, // 操作加载状态
                modalType: 0, // 添加或编辑标识
                dictModalTitle: '',
                dictModalVisible: false,
                modalVisible: false,
                modalTitle: '',
                editTitle: "", // 编辑节点名称
                dictForm: {
                    title: '',
                    type: '',
                    description: ''
                },
                form: {
                    // 添加或编辑表单对象初始化数据
                    title: '',
                    status: 1,
                    description: "",
                    sort: 0
                },
                dictFormValidate: {
                    // 表单验证规则
                    title: [{required: true, message: "不能为空", trigger: "blur"}],
                    type: [{required: true, message: "不能为空", trigger: "blur"}]
                },
                formValidate: {
                    // 表单验证规则
                    title: [{ required: true, message: "不能为空", trigger: "blur" }],
                    value: [{ required: true, message: "不能为空", trigger: "blur" }]
                },
                selectNode: {},
                selectCount: 0, // 多选计数
                selectList: [], // 多选数据
                searchForm: {
                    // 搜索框对应data对象
                    // name: "",
                    // status: "",
                    // pageNumber: 1,  // 当前页数
                    // pageSize: 10,   // 页面大小
                    // sort: "sort",   // 默认排序字段
                    // order: "asc"    // 默认排序方式
                },
                expand: true,
                span: 18,
                expandIcon: "ios-arrow-back",
                columns: [
                    // 表头
                    {
                        type: "selection",
                        width: 60,
                        align: "center"
                    },
                    {
                        type: "index",
                        width: 60,
                        align: "center"
                    },
                    {
                        title: "名称",
                        key: "title",
                        width: 150,
                        sortable: true
                    },
                    {
                        title: "数据值",
                        key: "value",
                        width: 150,
                        sortable: true
                    },
                    {
                        title: "备注",
                        key: "description",
                        width: 150,
                        sortable: true
                    },
                    {
                        title: "排序值",
                        key: "sort",
                        width: 100,
                        sortable: true,
                        sortType: "asc"
                    },
                    {
                        title: "状态",
                        key: "status",
                        align: "center",
                        width: 130,
                        render: (h, params) => {
                            let re = "";
                            if (params.row.status === 1) {
                                return h("div", [
                                    h(
                                        "Tag",
                                        {
                                            props: {
                                                color: "green"
                                            }
                                        },
                                        "正常启用"
                                    )
                                ]);
                            } else if (params.row.status === 0) {
                                return h("div", [
                                    h(
                                        "Tag",
                                        {
                                            props: {
                                                color: "red"
                                            }
                                        },
                                        "禁用"
                                    )
                                ]);
                            }
                        }
                    },
                    {
                        title: "创建时间",
                        key: "created_at",
                        width: 250,
                        sortable: true
                    },
                    {
                        title: "操作",
                        key: "action",
                        width: 160,
                        align: "center",
                        fixed: "right",
                        render: (h, params) => {
                            return h("div", [
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
                                                this.edit(params.row);
                                            }
                                        }
                                    },
                                    "编辑"
                                ),
                                h(
                                    "Button",
                                    {
                                        props: {
                                            type: "error",
                                            size: "small"
                                        },
                                        on: {
                                            click: () => {
                                                this.remove(params.row);
                                            }
                                        }
                                    },
                                    "删除"
                                )
                            ]);
                        }
                    }
                ],
                data: [],   //表单数据
                total: 0    // 表单数据总数
            }
        },
        methods: {
            init() {
                this.treeLoading = true;
                // 获取树数据
                this.getAllDict();
                // 获取表单数据
                this.getDataList();
            },
            getAllDict() {
                getAllDictList().then(res => {
                    this.treeData = res.result;
                    this.treeLoading = false;
                });
            },
            addDict() {
                this.modalType = 0;
                this.dictModalTitle = "添加字典";
                this.$refs.dictForm.resetFields();
                this.dictModalVisible = true;
            },
            handelSubmitDict() {
                this.$refs.dictForm.validate(valid => {
                    if (valid) {
                        this.submitLoading = true;
                        if (this.modalType === 0) {
                            // 添加 避免编辑后传入id等数据 记得删除
                            delete this.dictForm.id;
                            console.log(this.dictForm);
                            addDict(this.dictForm).then(res => {
                                this.submitLoading = false;
                                if (res.result === true) {
                                    this.$Message.success("添加成功");
                                    this.dictModalVisible = false;
                                    this.treeLoading = true;
                                    this.getAllDict();
                                    this.treeLoading = false;
                                }
                            });
                        }
                        else if (this.modalType === 1) {
                            // 编辑
                            editDict(this.dictForm).then(res => {
                                this.submitLoading = false;
                                if (res.result === true) {
                                    this.$Message.success("修改成功");
                                    this.getAllDict();
                                    this.dictModalVisible = false;
                                }
                            });
                        }
                    }
                });
            },
            handelSubmit() {
                this.$refs.form.validate(valid => {
                    if (valid) {
                        this.submitLoading = true;
                        if (this.modalType === 0) {
                            // 添加 避免编辑后传入id等数据 记得删除
                            delete this.form.id;
                            this.form.dict_id = this.selectNode.id;
                            addDictData(this.form).then(res => {
                                this.submitLoading = false;
                                if (res.result === true) {
                                    this.$Message.success("添加成功");
                                    this.getDataList();
                                    this.modalVisible = false;
                                }
                            });
                        } else if (this.modalType === 1) {
                            // 编辑
                            editDictData(this.form).then(res => {
                                this.submitLoading = false;
                                if (res.result === true) {
                                    this.$Message.success("修改成功");
                                    this.getDataList();
                                    this.modalVisible = false;
                                }
                            });
                        }
                    }
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
                    this.selectNode = data;
                    this.dictForm = data;
                    this.editTitle = data.title + "(" + data.type + ")";
                    // 重新加载表
                    // this.searchForm.pageNumber = 1;
                    // this.searchForm.pageSize = 10;
                    this.getDataList();
                } else {
                    this.cancelEdit();
                }
            },
            handleDropdown(name) {
                if (name == "editDict") {
                    if (!this.selectNode.id) {
                        this.$Message.warning("您还未选择要编辑的字典");
                        return;
                    }
                    this.editDict();
                } else if (name == "delDict") {
                    this.delDict();
                } else if (name == "refreshDict") {
                    this.refreshDict();
                }
            },
            editDict() {
                this.modalType = 1;
                this.dictModalTitle = "编辑字典";
                this.dictModalVisible = true;
            },
            delDict() {
                if (!this.selectNode.id) {
                    this.$Message.warning("您还未选择要删除的字典!");
                    return;
                }
                this.$Modal.confirm({
                    title: "确认删除",
                    loading: true,
                    content: "您确认要删除【" + this.selectNode.title + "】？",
                    onOk: () => {
                        // 删除
                        deleteDict(this.selectNode.id).then(res => {
                            this.$Modal.remove();
                            if (res.result === true) {
                                this.$Message.success("删除成功");
                                this.refreshDict();
                            }
                        });
                    }
                });
            },
            refreshDict() {
                this.treeLoading = true;
                this.getAllDict();
                this.selectNode = {};
                this.editTitle = "";
                this.getDataList();
            },
            cancelEdit() {
                let data = this.$refs.tree.getSelectedNodes()[0];
                if (data) {
                    data.selected = false;
                }
                // 取消选择后获取全部数据
                this.selectNode = {};
                this.editTitle = "";
                this.getDataList();
            },
            getDataList() {
                this.loading = true;
                if (this.selectNode.id) {
                    this.searchForm.dict_id = this.selectNode.id;
                } else {
                    delete this.searchForm.dict_id;
                }
                getAllDictDataList(this.searchForm).then(res => {
                    this.loading = false;
                    if (res.result) {
                        this.data = res.result;
                        this.total = res.result.length;
                    }
                });
            },
            changeExpand() {
                this.expand = !this.expand;
                if (this.expand) {
                    this.expandIcon = "ios-arrow-back";
                    this.span = 18;
                } else {
                    this.expandIcon = "ios-arrow-forward";
                    this.span = 23;
                }
            },
            add() {
                if (!this.selectNode.id) {
                    this.$Message.warning("请先选择一个字典类别");
                    return;
                }
                this.modalType = 0;
                this.modalTitle = "添加字典 " + this.editTitle + " 的数据";
                this.$refs.form.resetFields();
                this.modalVisible = true;
            },
            edit(v) {
                this.modalType = 1;
                this.modalTitle = "编辑字典 " + this.editTitle + " 的数据";
                this.$refs.form.resetFields();
                // 转换null为""
                for (let attr in v) {
                    if (v[attr] === null) {
                        v[attr] = "";
                    }
                }
                let str = JSON.stringify(v);
                let data = JSON.parse(str);
                this.form = data;
                this.modalVisible = true;
            },
            remove(v) {
                this.$Modal.confirm({
                    title: "确认删除",
                    loading: true,
                    content: "您确认要删除【" + v.title + "】？",
                    onOk: () => {
                        // 删除
                        deleteDictData(v.id).then(res => {
                            this.$Modal.remove();
                            if (res.result === true) {
                                this.$Message.success("删除成功");
                                this.getDataList();
                            }
                        });
                    }
                });
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
                        this.selectList.forEach(function(e) {
                            ids += e.id + ",";
                        });
                        ids = ids.substring(0, ids.length - 1);
                        // 批量删除
                        deleteDictData(ids).then(res => {
                            this.$Modal.remove();
                            if (res.result === true) {
                                this.$Message.success("操作成功");
                                this.clearSelectAll();
                                this.getDataList();
                            }
                        });
                    }
                });
            },
            clearSelectAll() {
                this.$refs.table.selectAll(false);
            },
            showSelect(e) {
                this.selectList = e;
                this.selectCount = e.length;
            },
            changeSort(e) {
                this.searchForm.sort = e.key;
                this.searchForm.order = e.order;
                if (e.order === "normal") {
                    this.searchForm.order = "";
                }
                this.getDataList();
            },
        },
        mounted() {
            this.init();
        }
    }
</script>

<style scoped>

</style>