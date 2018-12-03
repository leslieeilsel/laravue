import request from '../utils/request'

/**
 * 获取table数据
 * @param startMonth 开始时间
 * @param endMonth 结束时间
 * @returns {*}
 */
export function getTableData(startMonth, endMonth) {
  return request({
    url: '/api/tabledatas',
    method: 'post',
    data: {
      startMonth,
      endMonth
    }
  })
}
