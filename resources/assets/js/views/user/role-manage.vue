<template>
    <div>
        <Card>
            <p class="btnGroup">
                <Button type="primary" @click="modal = true" icon="md-add">添加</Button>
                <Button type="error" disabled icon="md-trash">删除</Button>
                <Modal
                    v-model="modal"
                    @on-cancel="cancel"
                    title="添加角色">
                    <Form ref="formValidate" :model="form" :rules="ruleValidate" :label-width="130">
                        <FormItem label="角色名称" prop="name">
                            <Input v-model="form.name" placeholder="必填项"></Input>
                        </FormItem>
                        <FormItem label="是否设置为默认角色" prop="is_default">
                            <i-switch v-model="form.is_default" size="large">
                                <span slot="open">是</span>
                                <span slot="close">否</span>
                            </i-switch>
                        </FormItem>
                        <FormItem label="备注" prop="description">
                            <Input v-model="form.description" placeholder="可选项"></Input>
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
    import './users.css';
    import { add, getRoles } from 'api/role';
    export default {
        data() {
            return {
                loading: false,
                loadingTable: true,
                modal: false,
                form: {
                    name: '',
                    description: '',
                    is_default: false,
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
                        title: '操作',
                        key: 'action',
                        width: 150,
                        align: 'center',
                        render: (h, params) => {
                            return h('div', [
                                h('Button', {
                                    props: {
                                        type: 'warning',
                                        size: 'small'
                                    },
                                    style: {
                                        marginRight: '5px'
                                    },
                                    on: {
                                        click: () => {
                                            this.show(params.index)
                                        }
                                    }
                                }, '菜单权限')
                            ]);
                        }
                    }
                ],
                data: []
            }
        },
        mounted() {
            getRoles().then((data) => {
                this.data = data.result;
                this.loadingTable = false;
            });
        },
        methods: {
            handleSubmit(name) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        this.loading = true;
                        add(this.form).then((response) => {
                            this.loading = false;
                            this.$Message.success('创建成功');
                            this.$refs[name].resetFields();
                            this.modal = false;
                            this.loadingTable = true;
                            getRoles().then((data) => {
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
            onChange(value) {
                this.form.department_id = value;
            },
        }
    }
</script>
