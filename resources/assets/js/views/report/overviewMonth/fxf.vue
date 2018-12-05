<template>
    <div>
        <Card>
            <Row>
                <Col span="12">
                    <DatePicker id="startMonth" type="month" placeholder="开始时间" style="width: 200px" :editable=false @on-change="startChange"></DatePicker>
                    <DatePicker id="endMonth" type="month" placeholder="结束时间" style="width: 200px" :editable=false @on-change="endChange"></DatePicker>
                    <Button type="primary" @click="filterData" :disabled="disable" icon="ios-search">查询</Button>
                </Col>
                <Button class="exportReport" :loading="expLoading" @click="exportData" type="primary" :disabled="btnDisable" icon="md-cloud-upload">导出报表</Button>
            </Row>
            <Table :columns="columns" :loading="loading" :data="data" border class="default" stripe size="small" ref="table"></Table>
        </Card>
    </div>
</template>
<script>
    import { getOverviewMonthData, exportOverviewMonthData } from 'api/report';
    import './table.css';
    export default {
        data () {
            return {
                expLoading: false,
                btnDisable: true,
                disable: true,
                loading: false,
                columns: [
                    {
                        title: '市区',
                        key: '0',
                        align: 'center',
                        width: 120,
                        fixed: 'left',
                    },
                    {
                        title: '月体育彩票销量',
                        align: 'center',
                        children: [
                            {
                                title: '概率游戏',
                                key: 'tc1',
                                align: 'right',
                                width: 100
                            },
                            {
                                title: '大乐透',
                                key: 'tc2',
                                align: 'right',
                                width: 100
                            },
                            {
                                title: '排三',
                                key: 'tc3',
                                align: 'right',
                                width: 100
                            },
                            {
                                title: '11选5',
                                key: 'tc4',
                                align: 'right',
                                width: 100
                            },
                            {
                                title: '竞彩',
                                key: 'tc5',
                                align: 'right',
                                width: 120
                            },
                            {
                                title: '足彩',
                                key: 'tc6',
                                align: 'right',
                                width: 100
                            },
                            {
                                title: '即开型',
                                key: 'tc7',
                                align: 'right',
                                width: 100
                            },
                            {
                                title: '总销量',
                                key: 'tc8',
                                align: 'right',
                                width: 120
                            }
                        ]
                    },
                    {
                        title: '月分配彩票发行费',
                        align: 'center',
                        children: [
                            {
                                title: '概率游戏',
                                key: 'fee0',
                                align: 'right',
                                width: 100
                            },
                            {
                                title: '大乐透',
                                key: 'fee1',
                                align: 'right',
                                width: 100
                            },
                            {
                                title: '排三',
                                key: 'fee2',
                                align: 'right',
                                width: 100
                            },
                            {
                                title: '11选5',
                                key: 'fee3',
                                align: 'right',
                                width: 100
                            },
                            {
                                title: '竞彩',
                                key: 'fee4',
                                align: 'right',
                                width: 100
                            },
                            {
                                title: '足彩',
                                key: 'fee5',
                                align: 'right',
                                width: 100
                            },
                            {
                                title: '即开型',
                                key: 'fee6',
                                align: 'right',
                                width: 100
                            }
                        ]
                    },
                    {
                        title: '发行费合计',
                        key: 'fee7',
                        align: 'right',
                        width: 120,
                        fixed: 'right'
                    }
                ],
                data: [],
                startValue: null,
                endValue: null,
                startArray: [],
                endArray: []
            }
        },
        mounted () {
        },
        methods: {
            startChange (daterange) {
                this.startValue = daterange;
                this.disable = !(this.startValue && this.endValue);
            },
            endChange (daterange) {
                this.endValue = daterange;
                this.disable = !(this.startValue && this.endValue);
            },
            filterData () {
                const startArray = this.startValue.split('-');
                const endArray = this.endValue.split('-');
                if (endArray[0] > startArray[0] || (endArray[0] === startArray[0] && endArray[1] >= startArray[1])) {
                    this.loading = true;
                    let _this = this;
                    getOverviewMonthData(this.startValue, this.endValue, 'fxf').then(function(result) {
                        _this.data = result.result;
                        _this.loading = false;
                        _this.btnDisable = false;
                    }).catch(function(){
                        alert("出错了");
                    });
                } else {
                    this.$Message.info({
                        content: '请重新选择时间！',
                        duration: 5,
                        closable: true
                    });
                }
            },
            exportData () {
                    // this.$message.loading('Action in progress..', 2.5)
                    //     .then(() => this.$message.success('Loading finished', 2.5))
                    //     .then(() => this.$message.info('Loading finished is finished', 2.5));
                // this.$Message.info('执行到这里了！');
                this.expLoading = true;
                let _this = this;
                exportOverviewMonthData(this.startValue, this.endValue, 'fxf').then(function(result) {
                    console.log(result.result)
                    _this.expLoading = false;
                    _this.$Message.info('导出成功！');
                }).catch(function(){
                    _this.$Message.info('导出失败！');
                });
            }
        }
    }
</script>
