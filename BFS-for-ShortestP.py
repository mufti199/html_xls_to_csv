# BFS for Single Source Shortest Path(SSSP)
class Graph:

    def __init__(self, start, end, count=0):

        self.start = start
        self.end = end
        self.count = count

    def is_valid(self, coordinate):
        if (coordinate[0] < 1 or coordinate[0] > 8) or (coordinate[1] < 1 or coordinate[1] > 8):
            return False
        return True

    def bfs(self):
        row = [2, 2, -2, -2, 1, 1, -1, -1]
        col = [1, -1, 1, -1, 2, -2, 2, -2]

        queue = []
        queue.append([self.start])
        while queue:
            path = queue.pop(0)
            node = path[-1]

            if node == self.end:
                print(len(path)-1)
                return print(path)
            for i in range(len(row)):
                new_path = list(path)
                nrow = node[0] + row[i]
                ncol = node[1] + col[i]
                if self.is_valid((nrow, ncol)):
                    new_path. append((nrow, ncol))
                    queue.append(new_path)


col_dict = {"a": 1, "b": 2, "c": 3, "d": 4, "e": 5, "f": 6, "g": 7, "h": 8}

my_graph = Graph((3,2), (2,5))
my_graph.bfs()


