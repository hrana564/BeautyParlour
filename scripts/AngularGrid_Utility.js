function Utility() {

    //Function to get the range for the Paging number
    this.range = function (min, virtualCount, size, step) {
        step = step || 1;
        var input = [];
        for (var i = min; i <= Math.ceil(virtualCount / size) ; i += step) input.push(i);
        return input;
    };

    //Function to get the display paging message
    this.Paging = function (VitualCount, PageSize, Index) {

        var PagingMessage = "";

        if (VitualCount > PageSize) {
            var Index2 = Index * PageSize;
            Index = (PageSize * Index) - (PageSize - 1);
            if (Index2 > VitualCount) {
                Index2 = VitualCount;
            }
            return PagingMessage = "Showing " + Index + " to " + Index2 + " of " + VitualCount + " entries";
        }
        else {
            return PagingMessage = "Showing " + Index + " to " + VitualCount + " of " + VitualCount + " entries";
        }
    }

    //Function to go for next page
    this.nextPage = function (currentPage, VirtualItemCount, PageSize, NoOfPages) {
        if (currentPage < Math.ceil(VirtualItemCount / PageSize)) {
            currentPage++;
            if (currentPage > 10) {
                Utility.range(currentPage, VirtualItemCount, currentPage + 10, 1)
            }
        }
        return currentPage;
    }

    //Function to go for previous page
    this.prevPage = function (currentPage) {
        if (currentPage > 1) {
            currentPage--;
        }
        return currentPage;
    }


}