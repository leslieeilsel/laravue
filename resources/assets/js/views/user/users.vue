<template>
    <div>
        <Card>
            <p class="btnGroup">
                <Button type="primary" @click="modal1 = true" icon="md-add">创建</Button>
                <Button type="error" disabled icon="md-trash">删除</Button>
                <Modal
                        v-model="modal1"
                        @on-cancel="cancel"
                        title="创建新用户">
                    <Form ref="formValidate" :model="form" :rules="ruleValidate" :label-width="80">
                        <FormItem label="用户名" prop="username">
                            <Input v-model="form.username" placeholder="必填项"></Input>
                        </FormItem>
                        <FormItem label="姓名" prop="name">
                            <Input v-model="form.name" placeholder="可选项"></Input>
                        </FormItem>
                        <FormItem label="邮箱" prop="email">
                            <Input v-model="form.email" placeholder="可选项"></Input>
                        </FormItem>
                        <FormItem label="联系电话" prop="phone">
                            <Input v-model="form.phone" placeholder="可选项"></Input>
                        </FormItem>
                        <FormItem label="所属部门" prop="department_id">
                            <a-tree-select
                                    :treeData="treeData"
                                    :showSearch="showSearch"
                                    :value="form.department_id"
                                    @change="onChange"
                                    :showCheckedStrategy="SHOW_PARENT"
                                    treeNodeFilterProp='label'
                                    placeholder='必填项'
                            />
                        </FormItem>
                        <FormItem label="密码" prop="password">
                            <Input v-model="form.password" type="password" placeholder="必填项"/>
                        </FormItem>
                        <FormItem label="确认密码" prop="pwdCheck">
                            <Input v-model="form.pwdCheck" type="password" placeholder="必填项"/>
                        </FormItem>
                        <FormItem label="备注" prop="desc">
                            <Input v-model="form.desc" type="textarea" :autosize="{minRows: 2,maxRows: 5}"
                                   placeholder="可选项"></Input>
                        </FormItem>
                        <FormItem>
                            <Button type="primary" @click="handleSubmit('formValidate')" :loading="loading">提交</Button>
                            <Button @click="handleReset('formValidate')" style="margin-left: 8px">重置</Button>
                        </FormItem>
                    </Form>
                    <div slot="footer">
                    </div>
                </Modal>
            </p>
            <Table border :columns="columns7" :data="nowData" :loading="loadingTable"></Table>
            <Page :total="dataCount" class-name="page-align" :page-size="pageSize" @on-change="changepage"
                  @on-page-size-change="_nowPageSize" show-total show-sizer show-elevator/>
        </Card>
    </div>
</template>
<script>
    import {registUser, getUsers} from 'api/user';
    import {getDeptSelecter} from 'api/department';
    import './users.css';
    import {TreeSelect} from 'ant-design-vue'

    const SHOW_PARENT = TreeSelect.SHOW_PARENT;

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
                if (this.form.password != '' && value == '') {
                    callback(new Error('确认密码不能为空'));
                } else if (this.form.password != value) {
                    callback(new Error('新密码和确认密码应相同'));
                } else {
                    callback();
                }
            };
            return {
                showSearch: true,
                loadingTable: true,
                loading: false,
                modal1: false,
                SHOW_PARENT,
                //分页
                pageSize: 10,//每页显示多少条
                dataCount: 0,//总条数
                pageCurrent: 1,//当前页
                form: {
                    username: '',
                    name: '',
                    email: '',
                    phone: '',
                    department_id: '0',
                    password: '',
                    pwdCheck: '',
                    desc: ''
                },
                ruleValidate: {
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
                    department_id: [
                        {required: true, message: '所属部门不能为空', trigger: 'blur'}
                    ]
                },
                columns7: [
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
                        title: '邮箱',
                        key: 'email'
                    },
                    {
                        title: '创建时间',
                        key: 'created_at',
                        sortable: true,
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
                                        type: 'primary',
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
                                }, 'View'),
                                h('Button', {
                                    props: {
                                        type: 'error',
                                        size: 'small'
                                    },
                                    on: {
                                        click: () => {
                                            this.remove(params.index)
                                        }
                                    }
                                }, 'Delete')
                            ]);
                        }
                    }
                ],
                data6: [],
                treeData: [],
                nowData: [],
                rowSelection,
            }
        },
        mounted() {
            getUsers().then((data) => {
                this.data6 = data.result;
                this.loadingTable = false;
                //分页显示所有数据总数
                this.dataCount = this.data6.length;
                //循环展示页面刚加载时需要的数据条数
                this.nowData = [];
                for (let i = 0; i < this.pageSize; i++) {
                    this.nowData.push(this.data6[i]);
                }
            });
            getDeptSelecter().then((data) => {
                this.treeData = data.result;
            });
        },
        methods: {
            show(index) {
                this.$Modal.info({
                    title: 'User Info',
                    content: `Name：${this.data6[index].name}<br>username：${this.data6[index].age}<br>Address：${this.data6[index].address}`
                })
            },
            remove(index) {
                this.data6.splice(index, 1);
            },
            handleSubmit(name) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        this.loading = true;
                        registUser(this.form).then((response) => {
                            this.loading = false;
                            this.$Message.success('创建成功');
                            this.$refs[name].resetFields();
                            this.modal1 = false;
                            this.loadingTable = true;
                            getUsers().then((data) => {
                                this.data6 = data.result;
                                this.loadingTable = false;
                                //分页显示所有数据总数
                                this.dataCount = this.data6.length;
                                //循环展示页面刚加载时需要的数据条数
                                this.nowData = [];
                                for (let i = 0; i < this.pageSize; i++) {
                                    this.nowData.push(this.data6[i]);
                                }
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
            changepage(index) {
                //需要显示开始数据的index,(因为数据是从0开始的，页码是从1开始的，需要-1)
                let _start = (index - 1) * this.pageSize;
                //需要显示结束数据的index
                let _end = index * this.pageSize;
                //截取需要显示的数据
                this.nowData = this.data6.slice(_start, _end);
                //储存当前页
                this.pageCurrent = index;
            },
            //每页显示的数据条数
            _nowPageSize(index) {
                //实时获取当前需要显示的条数
                this.pageSize = index;
            },
        }
    }
</script>
