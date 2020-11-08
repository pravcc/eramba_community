<?php
trait BulkActionsTrait  {
    protected function _readIds()
    {
        $request = $this->_request();
        $applyIds = $request->query['applyIds'];
        $applyIds = explode(',', $applyIds);

        return array_unique($applyIds);
    }
}
