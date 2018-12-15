<template>
    <div>
        <Card>
            <p class="btnGroup">
                <Button type="primary" @click="modal1 = true" icon="md-add">添加</Button>
                <Button type="error" disabled icon="md-trash">删除</Button>
                <Modal
                    v-model="modal1"
                    @on-cancel="cancel"
                    title="添加组织机构">
                    <Form ref="formValidate" :model="form" :rules="ruleValidate" :label-width="80">
                        <FormItem label="名称" prop="name">
                            <Input v-model="form.name" placeholder="必填项"></Input>
                        </FormItem>
                        <FormItem label="所属部门" prop="parent_id">
                            <a-tree-select
                                :treeData="treeData"
                                :showSearch="showSearch"
                                :value="form.parent_id"
                                @change="onChange"
                                :showCheckedStrategy="SHOW_PARENT"
                                treeNodeFilterProp='label'
                                placeholder='必填项'
                            />
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
            <a-table :columns="columns" :dataSource="data" :loading="loadingTable" bordered :rowSelection="rowSelection"
                     size="middle"/>
        </Card>
    </div>
</template>
<script>
    import {getDeptlist, getDeptSelecter, add} from 'api/department';
    import './departments.css';
    import {TreeSelect} from 'ant-design-vue'

    const SHOW_PARENT = TreeSelect.SHOW_PARENT;
    const columns = [{
        title: '名称',
        dataIndex: 'name',
        key: 'name',
    }, {
        title: '备注',
        dataIndex: 'desc',
        key: 'desc',
        width: '12%',
    }, {
        title: '创建时间',
        dataIndex: 'created_at',
        width: '30%',
        key: 'created_at',
    }];

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
            return {
                showSearch: true,
                loadingTable: true,
                loading: false,
                modal1: false,
                data: [],
                treeData: [],
                SHOW_PARENT,
                form: {
                    name: '',
                    parent_id: '0',
                    desc: ''
                },
                ruleValidate: {
                    name: [
                        {required: true, message: '名称不能为空', trigger: 'blur'}
                    ],
                    parent_id: [
                        {required: true, message: '所属部门不能为空', trigger: 'blur'}
                    ]
                },
                columns,
                rowSelection,
            }
        },
        mounted() {
            getDeptlist().then((data) => {
                this.data = data.result;
                this.loadingTable = false;
            });
            getDeptSelecter().then((data) => {
                this.treeData = data.result;
            });
        },
        methods: {
            handleSubmit(name) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        this.loading = true;
                        add(this.form).then((data) => {
                            if (data.result === true) {
                                this.loading = false;
                                this.$Message.success('创建成功');
                                this.$refs[name].resetFields();
                                this.modal1 = false;
                                this.loadingTable = true;
                                getDeptlist().then((data) => {
                                    this.data = data.result;
                                    this.loadingTable = false;
                                });
                                getDeptSelecter().then((data) => {
                                    this.treeData = data.result;
                                });
                            } else {
                                this.loading = false;
                                this.$Message.success('创建失败');
                                this.$refs[name].resetFields();
                                this.modal1 = false;
                            }
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
            onChange(value) {
                this.form.parent_id = value;
            }
        }
    }
</script>
